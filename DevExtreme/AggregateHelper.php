<?php
namespace DevExtreme;
class AggregateHelper {
    const MIN_OP = "MIN";
    const MAX_OP = "MAX";
    const AVG_OP = "AVG";
    const COUNT_OP = "COUNT";
    const AS_OP = "AS";
    const GENERATED_FIELD_PREFIX = "dx_";
    private static function _RecalculateGroupCountAndSummary(&$dataItem, $groupInfo) {
        if ($groupInfo["groupIndex"] <= $groupInfo["groupCount"] - 3) {
            $items = $dataItem["items"];
            foreach ($items as $item) {
                $grInfo = $groupInfo;
                $grInfo["groupIndex"]++;
                self::_RecalculateGroupCountAndSummary($item, $grInfo);
            }
        }
        if (isset($groupInfo["summaryTypes"]) && $groupInfo["groupIndex"] < $groupInfo["groupCount"] - 2) {
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
                foreach ($groupInfo["summaryTypes"] as $si => $stItem) {
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
            foreach ($groupInfo["summaryTypes"] as $si => $stItem) {
                if ($stItem == self::AVG_OP) {
                    $result[$si] /= $itemsCount;
                }
            }
            $dataItem["summary"] = $result;
        }
    }
    private static function _GetNewDataItem($row, $groupInfo) {
        $dataItem = array();
        $dataFieldCount = count($groupInfo["dataFieldNames"]);
        for ($index = 0; $index < $dataFieldCount; $index++) {
            $dataItem[$groupInfo["dataFieldNames"][$index]] = $row[$groupInfo["groupCount"] + $index];
        }
        return $dataItem;
    }
    private static function _GetNewGroupItem($row, $groupInfo) {
        $groupIndexOffset = $groupInfo["lastGroupExpanded"] ? 1 : 2;
        $groupItem = array();
        $groupItem["key"] = $row[$groupInfo["groupIndex"]];
        $groupItem["items"] = $groupInfo["groupIndex"] < $groupInfo["groupCount"] - $groupIndexOffset ? array() :
                                                                                                          ($groupInfo["lastGroupExpanded"] ? array() : NULL);
        if ($groupInfo["groupIndex"] == $groupInfo["groupCount"] - $groupIndexOffset) {
            if (isset($groupInfo["summaryTypes"])) {
                $summaries = array();
                $endIndex = $groupInfo["groupIndex"] + count($groupInfo["summaryTypes"]) + 1;
                for ($index = $groupInfo["groupCount"]; $index <= $endIndex; $index++) {
                    $summaries[] = $row[$index];
                }
                $groupItem["summary"] = $summaries;
            }
            if (!$groupInfo["lastGroupExpanded"]) {
                $groupItem["count"] = $row[$groupInfo["groupIndex"] + 1];
            }
            else {
                $groupItem["items"][] = self::_GetNewDataItem($row, $groupInfo);
            }
        }
        return $groupItem;
    }
    private static function _GroupData($row, &$resultItems, $groupInfo) {
        $itemsCount = count($resultItems);
        if (!isset($row) && !$itemsCount) {
            return;
        }
        $currentItem = NULL;
        $groupIndexOffset = $groupInfo["lastGroupExpanded"] ? 1 : 2;
        if ($itemsCount) {
            $currentItem = &$resultItems[$itemsCount - 1];
            if (!$groupInfo["lastGroupExpanded"]) {
                if ($currentItem["key"] != $row[$groupInfo["groupIndex"]] || !isset($row)) {
                    if ($groupInfo["groupIndex"] == 0 && $groupInfo["groupCount"] > 2) {
                        self::_RecalculateGroupCountAndSummary($currentItem, $groupInfo);
                    }
                    unset($currentItem);
                    if (!isset($row)) {
                        return;
                    }
                }
            }
            else {
                if ($currentItem["key"] != $row[$groupInfo["groupIndex"]]) {
                    unset($currentItem);
                }
                else {
                    if ($groupInfo["groupIndex"] == $groupInfo["groupCount"] - $groupIndexOffset) {
                        $currentItem["items"][] = self::_GetNewDataItem($row, $groupInfo);
                    }
                }
            }
        }
        if (!isset($currentItem)) {
            $currentItem = self::_GetNewGroupItem($row, $groupInfo);
            $resultItems[] = &$currentItem;
        }
        if ($groupInfo["groupIndex"] < $groupInfo["groupCount"] - $groupIndexOffset) {
            $groupInfo["groupIndex"]++;
            self::_GroupData($row, $currentItem["items"], $groupInfo);
        }
    }
    public static function GetGroupedDataFromQuery($queryResult, $groupSettings) {
        $result = array();
        $row = NULL;
        $groupSummaryTypes = NULL;
        $dataFieldNames = NULL;
        $startSummaryFieldIndex = NULL;
        $endSummaryFieldIndex = NULL;
        if ($groupSettings["lastGroupExpanded"]) {
            $queryFields = $queryResult->fetch_fields();
            $dataFieldNames = array();
            for ($i = $groupSettings["groupCount"]; $i < count($queryFields); $i++) {
                $dataFieldNames[] = $queryFields[$i]->name;
            }
        }
        if (isset($groupSettings["summaryTypes"])) {
            $groupSummaryTypes = $groupSettings["summaryTypes"];
            $startSummaryFieldIndex = $groupSettings["groupCount"] - 1;
            $endSummaryFieldIndex = $startSummaryFieldIndex + count($groupSummaryTypes);
        }
        $groupInfo = array(
            "groupCount" => $groupSettings["groupCount"],
            "groupIndex" => 0,
            "summaryTypes" => $groupSummaryTypes,
            "lastGroupExpanded" => $groupSettings["lastGroupExpanded"],
            "dataFieldNames" => $dataFieldNames
        );
        while ($row = $queryResult->fetch_array(MYSQLI_NUM)) {
            if (isset($startSummaryFieldIndex)) {
                for ($i = $startSummaryFieldIndex; $i <= $endSummaryFieldIndex; $i++) {
                    $row[$i] = Utils::StringToNumber($row[$i]);
                }
            }
            self::_GroupData($row, $result, $groupInfo);
        }
        if (!$groupSettings["lastGroupExpanded"]) {
            self::_GroupData($row, $result, $groupInfo);
        }
        else {
            if (isset($groupSettings["skip"]) && $groupSettings["skip"] >= 0 &&
                isset($groupSettings["take"]) && $groupSettings["take"] >= 0) {
                $result = array_slice($result, $groupSettings["skip"], $groupSettings["take"]);
            }
        }
        return $result;
    }
    public static function IsLastGroupExpanded($items) {
        $result = true;
        $itemsCount = count($items);
        if ($itemsCount > 0) {
            $lastItem = $items[$itemsCount - 1];
            if (gettype($lastItem) === "object") {
                $result = isset($lastItem->isExpanded) ? $lastItem->isExpanded === true : true;
            }
            else {
                $result = true;
            }
        }
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
                $selectField = $groupField = $sortField = Utils::QuoteStringValue($item);
            }
            else if (gettype($item) === "object" && isset($item->selector)) {
                $quoteSelector = Utils::QuoteStringValue($item->selector);
                $desc = isset($item->desc) ? $item->desc : false;
                if (isset($item->groupInterval)) {
                    if (is_int($item->groupInterval)) {
                        $groupField = Utils::QuoteStringValue(sprintf("%s%s_%d", self::GENERATED_FIELD_PREFIX, $item->selector, $item->groupInterval));
                        $selectField = sprintf("(%s - (%s %% %d)) %s %s",
                                               $quoteSelector,
                                               $quoteSelector,
                                               $item->groupInterval,
                                               self::AS_OP,
                                               $groupField);
                    }
                    else {
                        $groupField = Utils::QuoteStringValue(sprintf("%s%s_%s", self::GENERATED_FIELD_PREFIX, $item->selector, $item->groupInterval));
                        $selectField = sprintf("%s(%s) %s %s",
                                               strtoupper($item->groupInterval),
                                               $quoteSelector,
                                               self::AS_OP,
                                               $groupField);
                    }
                    $sortField = $groupField;
                }
                else {
                    $selectField = $groupField = $sortField = $quoteSelector;
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
                $fields .= sprintf("%s(%s) %s %sf%d",
                                   strlen($fields) > 0 ? ", ".$summaryTypes[$index] : $summaryTypes[$index],
                                   (isset($item->selector) && is_string($item->selector)) ? Utils::QuoteStringValue($item->selector) : "1",
                                   self::AS_OP,
                                   self::GENERATED_FIELD_PREFIX,
                                   $index);
            }
        }
        $result["fields"] = $fields;
        $result["summaryTypes"] = $summaryTypes;
        return $result;
    }
}
