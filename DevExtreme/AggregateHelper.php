<?php
namespace DevExtreme;
class AggregateHelper {
    const MIN_OP = "MIN";
    const MAX_OP = "MAX";
    const AVG_OP = "AVG";
    const COUNT_OP = "COUNT";
    const AS_OP = "AS";
    private static function _RecalculateGroupCountAndSummary(&$dataItem, $groupCount, $groupIndex, $summaryTypes = NULL) {
        if ($groupIndex <= $groupCount - 3) {
            $items = $dataItem["items"];
            $dataItem["count"] = count($items);
            foreach ($items as $item) {
                self::_RecalculateGroupCountAndSummary($item, $groupCount, $groupIndex + 1, $summaryTypes);
            }
        }
        if (isset($summaryTypes) && $groupIndex < $groupCount - 2) {
            $result = array();
            $items = $dataItem["items"];
            $itemsCount = count($items);
            foreach ($items as $index => $item) {
                $currentSummaries = $item["summary"];
                if ($index == 0) {
                    foreach ($currentSummaries as $summaryItem) {
                        $result[] = $summaryItem;
                    }
                    continue;
                }
                foreach ($summaryTypes as $si => $stItem) {
                    if ($stItem == self::MIN_OP) {
                        if ($result[$si] > $currentSummaries[$si]) {
                            $result[$si] = $currentSummaries[$si];
                        }
                        continue;
                    }
                    if ($stItem == self::MAX_OP) {
                        if ($result[$si] < $currentSummaries[$si]) {
                            $result[$si] = $currentSummaries[$si];
                        }
                        continue;
                    }
                    $result[$si] += $currentSummaries[$si];
                }
            }
            foreach ($summaryTypes as $si => $stItem) {
                if ($stItem == self::AVG_OP) {
                    $result[$si] /= $itemsCount;
                }
            }
            $dataItem["summary"] = $result;
        }
    }
    private static function _GroupData($row, &$resultItems, $groupCount, $groupIndex, $summaryTypes = NULL) {
        $itemsCount = count($resultItems);
        if (!isset($row) && !$itemsCount) {
            return;
        }
        $currentItem = NULL;
        if ($itemsCount) {
            $currentItem = &$resultItems[$itemsCount - 1];
            if ($currentItem["key"] != $row[$groupIndex] || !isset($row)) {
                if ($groupIndex == 0 && $groupCount > 2) {
                    self::_RecalculateGroupCountAndSummary($currentItem, $groupCount, $groupIndex, $summaryTypes);
                }
                unset($currentItem);
                if (!isset($row)) {
                    return;
                }
            }
        }
        if (!isset($currentItem)) {
            $currentItem = array();
            $resultItems[] = &$currentItem;
            $currentItem["key"] = $row[$groupIndex];
            $currentItem["items"] = $groupIndex < $groupCount - 2 ? array() : NULL;
            if ($groupIndex == $groupCount - 2) {
                if (isset($summaryTypes)) {
                    $summaries = array();
                    $endIndex = $groupIndex + count($summaryTypes) + 1;
                    for ($index = $groupCount; $index <= $endIndex; $index++) {
                        $summaries[] = $row[$index];
                    }
                    $currentItem["summary"] = $summaries;
                }
                $currentItem["count"] = $row[$groupIndex + 1];
            }
        }
        if ($groupIndex < $groupCount - 2) {
            self::_GroupData($row, $currentItem["items"], $groupCount, ++$groupIndex, $summaryTypes);
        }
    }
    public static function GetGroupedDataFromQuery($queryResult, $groupSettings) {
        $result = array();
        $row = NULL;
        $groupSummaryTypes = NULL;
        $startSummaryFieldIndex = $groupSettings["groupCount"] - 1;
        $endSummaryFieldIndex = $startSummaryFieldIndex;
        if (isset($groupSettings["summaryTypes"])) {
            $groupSummaryTypes = $groupSettings["summaryTypes"];
            $endSummaryFieldIndex = $startSummaryFieldIndex + count($groupSummaryTypes);
        }
        while ($row = $queryResult->fetch_array(MYSQLI_NUM)) {
            for ($i = $startSummaryFieldIndex; $i <= $endSummaryFieldIndex; $i++) {
                $row[$i] = Utils::StringToNumber($row[$i]);
            }
            self::_GroupData($row, $result, $groupSettings["groupCount"], 0, $groupSummaryTypes);
        }
        self::_GroupData($row, $result, $groupSettings["groupCount"], 0, $groupSummaryTypes);
        return $result;
    }
    public static function GetFieldSetBySelectors($items) {
        $group = "";
        $sort = "";
        $select = "";
        foreach ($items as $item) {
            $groupField = NULL;
            $sortField = NULL;
            $selectField = NULL;
            $desc = false;
            if (is_string($item) && strlen($item = trim($item))) {
                $groupField = $sortField = Utils::QuoteStringValue($item);
            }
            else if (gettype($item) === "object" && isset($item->selector)) {
                $quoteSelector = Utils::QuoteStringValue($item->selector);
                $desc = isset($item->desc) ? $item->desc : false;
                if (isset($item->groupInterval)) {
                    if (is_int($item->groupInterval)) {
                        $groupField = Utils::QuoteStringValue(sprintf("%s_%d", $item->selector, $item->groupInterval));
                        $selectField = sprintf("(%s - (%s %% %d)) %s %s",
                                               $quoteSelector,
                                               $quoteSelector,
                                               $item->groupInterval,
                                               self::AS_OP,
                                               $groupField);
                    }
                    else {
                        $groupField = Utils::QuoteStringValue(sprintf("%s_%s", $item->selector, $item->groupInterval));
                        $selectField = sprintf("%s(%s) %s %s",
                                               strtoupper($item->groupInterval),
                                               $quoteSelector,
                                               self::AS_OP,
                                               $groupField);
                    }
                }
                else {
                    $groupField = $sortField = $quoteSelector;
                }
            }
            if (isset($selectField)) {
                $select .= (strlen($select) > 0 ? ", ".$selectField : $selectField);
            }
            if (isset($groupField)) {
                $group .= (strlen($group) > 0 ? ", ".$groupField : $groupField);
            }
            if (isset($sortField)) {
                $sort .= (strlen($sort) > 0 ? ", ".$sortField : $sortField).
                         ($desc ? " DESC" : "");
            }
        }
        return array(
            "group" => $group,
            "sort" => $sort,
            "select" => $select
        );
    }
    public static function GetSummaryInfo($expression) {
        $result = array();
        $fields = "";
        $summaryTypes = array();
        foreach ($expression as $index => $item) {
            if (gettype($item) === "object" && isset($item->summaryType)) {
                $summaryTypes[] = strtoupper($item->summaryType);
                $fields .= sprintf("%s(%s) %s f%d",
                                   strlen($fields) > 0 ? ", ".$summaryTypes[$index] : $summaryTypes[$index],
                                   (isset($item->selector) && is_string($item->selector)) ? Utils::QuoteStringValue($item->selector) : "1",
                                   self::AS_OP,
                                   $index);
            }
        }
        $result["fields"] = $fields;
        $result["summaryTypes"] = $summaryTypes;
        return $result;
    }
}
