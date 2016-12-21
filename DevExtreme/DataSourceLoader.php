<?php
namespace DevExtreme;
class DataSourceLoader {
    public static function Load($dbSet, $params) {
        $result = NULL;
        if (isset($dbSet) && get_class($dbSet) == "DevExtreme\DbSet" && isset($params) && is_array($params)) {
            $dbSet->Select(Utils::GetItemValueOrDefault($params, "select"))
                  ->Filter(Utils::GetItemValueOrDefault($params, "filter"));
            $totalSummary = $dbSet->GetTotalSummary(Utils::GetItemValueOrDefault($params, "totalSummary"),
                                                    Utils::GetItemValueOrDefault($params, "filter"));
            if ($dbSet->GetLastError() !== NULL) {
                return $result;
            }
            $totalCount = (isset($params["requireTotalCount"]) && $params["requireTotalCount"] === true)
                          ? $dbSet->GetCount() : NULL;
            if ($dbSet->GetLastError() !== NULL) {
                return $result;
            }
            $dbSet->Sort(Utils::GetItemValueOrDefault($params, "sort"));
            $groupCount = NULL;
            $skip = Utils::GetItemValueOrDefault($params, "skip");
            $take = Utils::GetItemValueOrDefault($params, "take");
            if (isset($params["group"])) {
                $groupExpression = $params["group"];
                $groupSummary = Utils::GetItemValueOrDefault($params, "groupSummary");
                $dbSet->Group($groupExpression, $groupSummary, $skip, $take);
                if (isset($params["requireGroupCount"]) && $params["requireGroupCount"] === true) {
                    $groupCount = $dbSet->GetGroupCount();
                }
            }
            else {
                $dbSet->SkipTake($skip, $take);
            }
            $result = array();
            $result["data"] = $dbSet->AsArray();
            if ($dbSet->GetLastError() !== NULL) {
                return $result;
            }
            if (isset($totalCount)) {
                $result["totalCount"] = $totalCount;
            }
            if (isset($totalSummary)) {
                $result["summary"] = $totalSummary;
            }
            if (isset($groupCount)) {
                $result["groupCount"] = $groupCount;
            }
        }
        else {
            throw new \Exception("Invalid params");
        }
        return $result;
    }
}
