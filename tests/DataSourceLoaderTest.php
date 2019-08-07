<?php
require_once("TestBase.php");
use DevExtreme\DataSourceLoader;

class DataSourceLoaderTest extends TestBase {
    public function providerSort() {
        return array(
            array(array("Name"), "", false, "Name"),
            array(
                array(
                    (object)array(
                        "selector" => "Name",
                        "desc" => false
                    )
                ),
                "",
                false,
                "Name"
            ),
            array(
                array(
                    (object)array(
                        "selector" => "Name",
                        "desc" => true
                    )
                ),
                "Z",
                true,
                "Name"
            )
        );
    }
    public function providerFilter() {
        return array(
            array(
                array("ID", "=", 10),
                array(10)
            ),
            array(
                array(
                    array("ID", ">", 1),
                    "and",
                    array("ID", "<=", 3)
                ),
                array(2, 3)
            ),
            array(
                array("ID", ">=", 29),
                array(29, 30, 31)
            ),
            array(
                array("ID", "<", 2),
                array(1)
            ),
            array(
                array(
                    array("!", array("ID", "=", 2)),
                    "and",
                    array("ID", "<=", 3)
                ),
                array(1, 3)
            ),
            array(
                array("Name", "startswith", "Cha"),
                array(1, 2)
            ),
            array(
                array("Name", "endswith", "ku"),
                array(9)
            ),
            array(
                array("Name", "contains", "onb"),
                array(13)
            ),
            array(
                array(
                    array("Name", "notcontains", "A"),
                    "and",
                    array("Name", "notcontains", "a")
                ),
                array(9, 13, 14, 15, 21, 23, 26)
            ),
            array(
               array(
                   array("CustomerName", "<>", null),
                   "and",
                   array("ID", ">", 27)
               ),
               array(28, 29, 30)
            )
        );
    }
    public function providerGroup() {
        return array(
            array(array("Category"), "", false, "key", 4, array(10, 9, 4, 8)),
            array(
                array(
                    (object)array(
                        "selector" => "Category",
                        "desc" => false
                    )
                ),
                "",
                false,
                "key",
                4,
                array(10, 9, 4, 8)
            ),
            array(
                array(
                    (object)array(
                        "selector" => "Category",
                        "desc" => true,
                        "isExpanded" => false
                    )
                ),
                "Z",
                true,
                "key",
                4,
                array(8, 4, 9, 10)
            ),
            array(
                array(
                    (object)array(
                        "selector" => "BDate",
                        "groupInterval" => "year",
                        "desc" => true,
                        "isExpanded" => false
                    )
                ),
                "9999",
                true,
                "key",
                1,
                array(31)
            )
        );
    }
    public function providerGroupPaging() {
        $groupExpression1 = array(
            (object)array(
                "selector" => "Category",
                "desc" => false,
                "isExpanded" => false
            )
        );
        $groupExpression2 = array(
            (object)array(
                "selector" => "Category",
                "desc" => false,
                "isExpanded" => true
            )
        );
        $params1 = array(
            "requireGroupCount" => true,
            "group" => $groupExpression1,
            "skip" => 1,
            "take" => 2
        );
        $params2 = array(
            "requireGroupCount" => true,
            "group" => $groupExpression2,
            "skip" => 1,
            "take" => 2
        );
        $resultGroupItems = array("Condiments", "Dairy Products");
        return array(
            array($params1, $resultGroupItems),
            array($params2, $resultGroupItems)
        );
    }
    public function providerTotalSummary() {
        $summaryExpression1 = array(
            (object)array(
                "summaryType" => "count"
            )
        );
        $summaryExpression2 = array(
            (object)array(
                "selector" => "ID",
                "summaryType" => "min"
            )
        );
        $summaryExpression3 = array(
            (object)array(
                "selector" => "ID",
                "summaryType" => "max"
            )
        );
        $summaryExpression4 = array(
            (object)array(
                "selector" => "ID",
                "summaryType" => "sum"
            )
        );
        $summaryExpression5 = array(
            (object)array(
                "selector" => "ID",
                "summaryType" => "avg"
            )
        );
        return array(
            array($summaryExpression1, 31),
            array($summaryExpression2, 1),
            array($summaryExpression3, 31),
            array($summaryExpression4, 496),
            array($summaryExpression5, 16)
        );
    }
    public function testLoaderSelect() {
        $columns = array("BDate", "Category", "CustomerName");
        $params = array(
            "select" => $columns
        );
        $data = DataSourceLoader::Load($this->dbSet, $params);
        $result = isset($data) && is_array($data) && isset($data["data"]) && count($data["data"]) > 0 ?
                  array_keys($data["data"][0]) :
                  array();
        $this->assertEquals($columns, $result);
    }
    public function testLoaderTotalCount() {
        $params = array(
            "requireTotalCount" => true
        );
        $data = DataSourceLoader::Load($this->dbSet, $params);
        $result = isset($data) && is_array($data) &&
                  isset($data["data"]) && isset($data["totalCount"]) &&
                  count($data["data"]) == $data["totalCount"] && $data["totalCount"] == 31;
        $this->assertTrue($result);
    }
    /**
     * @dataProvider providerSort
     */
    public function testLoaderSort($sortExpression, $currentValue, $desc, $field) {
        $sorted = true;
        $params = array(
            "sort" => $sortExpression
        );
        $data = DataSourceLoader::Load($this->dbSet, $params);
        $result = isset($data) && isset($data["data"]) && is_array($data["data"]) ? $data["data"] : NULL;
        $dataItemsCount = isset($result) ? count($result) : 0;
        for ($i = 0; $i < $dataItemsCount; $i++) {
            $compareResult = strcmp($currentValue, $result[$i][$field]);
            if ((!$desc && $compareResult > 0) || ($desc && $compareResult < 0)) {
                $sorted = false;
                break;
            }
            $currentValue = $result[$i][$field];
        }
        $this->assertTrue($sorted && $dataItemsCount == 31);
    }
    public function testLoaderSkipTake() {
        $params = array(
            "skip" => 5,
            "take" => 10
        );
        $ids = array(6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
        $data = DataSourceLoader::Load($this->dbSet, $params);
        $result = isset($data) && isset($data["data"]) && is_array($data["data"]) ? $data["data"] : NULL;
        $itemsCount = isset($result) ? count($result) : 0;
        $paginated = true;
        if ($itemsCount != count($ids)) {
            $paginated = false;
        }
        else {
            for ($i = 0; $i < $itemsCount; $i++) {
                if ($result[$i]["ID"] != $ids[$i]) {
                    $paginated = false;
                    break;
                }
            }
        }
        $this->assertTrue($paginated);
    }
    /**
     * @dataProvider providerFilter
     */
    public function testLoaderFilter($expression, $ids) {
        $params = array(
            "filter" => $expression
        );
        $data = DataSourceLoader::Load($this->dbSet, $params);
        $result = isset($data) && isset($data["data"]) && is_array($data["data"]) ? $data["data"] : NULL;
        $itemsCount = isset($result) ? count($result) : 0;
        $filtered = true;
        if ($itemsCount != count($ids)) {
            $filtered = false;
        }
        else {
            for ($i = 0; $i < $itemsCount; $i++) {
                if ($result[$i]["ID"] != $ids[$i]) {
                    $filtered = false;
                    break;
                }
            }
        }
        $this->assertTrue($filtered);
    }
    /**
     * @dataProvider providerGroup
     */
    public function testLoaderGroup($groupExpression, $currentValue, $desc, $field, $groupCount, $itemsInGroups) {
        $grouped = true;
        $params = array(
            "group" => $groupExpression
        );
        $data = DataSourceLoader::Load($this->dbSet, $params);
        $result = isset($data) && isset($data["data"]) && is_array($data["data"]) ? $data["data"] : NULL;
        $dataItemsCount = isset($result) ? count($result) : 0;
        for ($i = 0; $i < $dataItemsCount; $i++) {
            $compareResult = strcmp($currentValue, strval($result[$i][$field]));
            $count = isset($groupExpression[0]->isExpanded) && $groupExpression[0]->isExpanded === false ? $result[$i]["count"] : count($result[$i]["items"]);
            if ((!$desc && $compareResult > 0) || ($desc && $compareResult < 0) || ($count != $itemsInGroups[$i])) {
                $grouped = false;
                break;
            }
            $currentValue = strval($result[$i][$field]);
        }
        $this->assertTrue($grouped && $dataItemsCount == $groupCount);
    }
    /**
     * @dataProvider providerGroupPaging
     */
    public function testLoaderGroupPaging($params, $resultGroupItems) {
        $data = DataSourceLoader::Load($this->dbSet, $params);
        $isPaginated = false;
        $groupCount = 0;
        if (isset($data) && isset($data["data"]) && isset($data["groupCount"]) && count($resultGroupItems) === count($data["data"])) {
            $groupItems = $data["data"];
            $isPaginated = true;
            foreach ($groupItems as $index => $groupItem) {
                if (strcmp($groupItem["key"], $resultGroupItems[$index]) !== 0) {
                    $isPaginated = false;
                    break;
                }
            }
            $groupCount = $data["groupCount"];
        }
        $this->assertTrue($isPaginated && $groupCount === 4);
    }
    /**
     * @dataProvider providerTotalSummary
     */
    public function testLoaderTotalSummary($summaryExpression, $value) {
        $params = array(
            "totalSummary" => $summaryExpression
        );
        $data = DataSourceLoader::Load($this->dbSet, $params);
        $result = isset($data) && is_array($data) && isset($data["summary"]) ? $data["summary"][0] : 0;
        $this->assertEquals($value, $result);
    }
}
