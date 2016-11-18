<?php
require_once("TestBase.php");

class DbSetAPITest extends TestBase {
    public function providerFilterAnd() {
        $filterExpression1 = array(
            array("Category", "=", "Dairy Products"),
            array("BDate", "=", "6/19/2013"),
            array("Name", "=", "Sir Rodney\'s Scones"),
            array("CustomerName", "=", "Fuller Andrew"),
            array("ID", "=", 21)
        );
        $filterExpression2 = array(
            array("Category", "=", "Dairy Products"),
            "and",
            array("BDate", "=", "2013-06-19"),
            "and",
            array("Name", "=", "Sir Rodney\'s Scones"),
            "and",
            array("CustomerName", "=", "Fuller Andrew"),
            "and",
            array("!", array("ID", "<>", 21))
        );
        $filterExpression3 = array(
            array("Category", "=", "Dairy Products"),
            array("BDate.year", "=", "2013"),
            array("Name", "=", "Sir Rodney\'s Scones"),
            array("CustomerName", "=", "Fuller Andrew"),
            array("ID", "=", 21)
        );
        $filterExpression4 = array(
            array("Category", "=", "Dairy Products"),
            array("BDate.month", "=", "6"),
            array("Name", "=", "Sir Rodney\'s Scones"),
            array("CustomerName", "=", "Fuller Andrew"),
            array("ID", "=", 21)
        );
        $filterExpression5 = array(
            array("Category", "=", "Dairy Products"),
            array("BDate.day", "=", "19"),
            array("Name", "=", "Sir Rodney\'s Scones"),
            array("CustomerName", "=", "Fuller Andrew"),
            array("ID", "=", 21)
        );
        $filterExpression6 = array(
            array("Category", "=", "Dairy Products"),
            array("BDate.dayOfWeek", "=", "3"),
            array("Name", "=", "Sir Rodney\'s Scones"),
            array("CustomerName", "=", "Fuller Andrew"),
            array("ID", "=", 21)
        );
        $values = array(21, "Sir Rodney's Scones", "Dairy Products", "Fuller Andrew", "2013-06-19");
        return array(
            array($filterExpression1, $values),
            array($filterExpression2, $values),
            array($filterExpression3, $values),
            array($filterExpression4, $values),
            array($filterExpression5, $values),
            array($filterExpression6, $values)
        );
    }
    public function providerSort() {
        $field = "Name";
        $sortExpression1 = array($field);
        $sortExpression2 = array(
            (object)array(
                "selector" => $field,
                "desc" => false
            )
        );
        $sortExpression3 = array(
            (object)array(
                "selector" => $field,
                "desc" => true
            )
        );
        return array(
            array($sortExpression1, "", false, $field),
            array($sortExpression2, "", false, $field),
            array($sortExpression3, "Z", true, $field)
        );
    }
    public function providerGroup() {
        $field = "Category";
        $groupCount = 4;
        $groupField = "key";
        $groupExpression1 = array($field);
        $groupExpression2 = array(
            (object)array(
                "selector" => $field,
                "desc" => false
            )
        );
        $groupExpression3 = array(
            (object)array(
                "selector" => $field,
                "desc" => true
            )
        );
        return array(
            array($groupExpression1, "", false, $groupField, $groupCount),
            array($groupExpression2, "", false, $groupField, $groupCount),
            array($groupExpression3, "Seafood", true, $groupField, $groupCount)
        );
    }
    private function GroupSummariesEqual($data, $standard) {
        $dataCount = count($data);
        $standardCount = count($standard);
        $result = $dataCount === $standardCount;
        if ($result) {
            for ($i = 0; $i < $dataCount; $i++) {
                $dataSummary = $data[$i]["summary"];
                $standardSummary = $standard[$i]["summary"];
                if (is_array($dataSummary) &&
                   (count($dataSummary) == count($standard[$i]["summary"])) &&
                   (count(array_diff($dataSummary, $standardSummary)) === 0)) {
                    if (isset($standard[$i]["items"])) {
                        if (isset($data[$i]["items"])) {
                            $result = $this->GroupSummariesEqual($data[$i]["items"], $standard[$i]["items"]);
                        }
                    }
                    if ($result) {
                        continue;
                    }
                }
                $result = false;
                break;
            }
        }
        return $result;
    }
    public function providerGroupSummary() {
        $group = array(
            (object)array(
                "selector" => "Category",
                "desc" => false
            ),
            (object)array(
                "selector" => "CustomerName",
                "desc" => true
            )
        );
        $groupSummary = array(
            (object)array(
                "selector" => "ID",
                "summaryType" => "min"
            ),
            (object)array(
                "selector" => "ID",
                "summaryType" => "max"
            ),
            (object)array(
                "selector" => "ID",
                "summaryType" => "sum"
            ),
            (object)array(
                "summaryType" => "count"
            )
        );
        $result = array(
            array(
                "summary" => array(3, 29, 141, 10),
                "items" => array(
                    array("summary" => array(5, 29, 56, 3)),
                    array("summary" => array(18, 18, 18, 1)),
                    array("summary" => array(3, 16, 23, 3)),
                    array("summary" => array(6, 23, 44, 3))
                )
            ),
            array(
                "summary" => array(1, 28, 138, 9),
                "items" => array(
                    array("summary" => array(1, 28, 41, 3)),
                    array("summary" => array(26, 26, 26, 1)),
                    array("summary" => array(8, 17, 34, 3)),
                    array("summary" => array(13, 24, 37, 2))
                )
            ),
            array(
                "summary" => array(2, 21, 34, 3),
                "items" => array(
                    array("summary" => array(2, 2, 2, 1)),
                    array("summary" => array(21, 21, 21, 1)),
                    array("summary" => array(11, 11, 11, 1))
                )
            ),
            array(
                "summary" => array(7, 30, 152, 8),
                "items" => array(
                    array("summary" => array(10, 20, 30, 2)),
                    array("summary" => array(27, 30, 57, 2)),
                    array("summary" => array(7, 25, 46, 3)),
                    array("summary" => array(19, 19, 19, 1))
                )
            )
        );
        return array(
            array($group, $groupSummary, $result)
        );
    }
    public function providerGetTotalSummary() {
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
            array($summaryExpression1, 30),
            array($summaryExpression2, 1),
            array($summaryExpression3, 30),
            array($summaryExpression4, 465),
            array($summaryExpression5, 15.5)
        );
    }
    public function testGetCount() {
        $this->assertEquals(30, $this->dbSet->GetCount());
    }
    public function testSelect() {
        $columns = array("BDate", "Category", "CustomerName");
        $this->dbSet->Select($columns);
        $data = $this->dbSet->AsArray();
        $result = count($data) > 0 ? array_keys($data[0]) : array();
        $this->assertEquals($columns, $result);
    }
    /**
     * @dataProvider providerFilterAnd
     */
    public function testFilterAnd($filterExpression, $values) {
        $this->dbSet->Filter($filterExpression);
        $data = $this->dbSet->AsArray();
        $result = count($data) > 0 ? array_values($data[0]) : array();
        $this->assertEquals($values, $result);
    }
    public function testFilterOr() {
        $filterExpression = array(
            array("ID", "=", 10),
            "or",
            array("ID", "=" , 20)
        );
        $values = array(10, 20);
        $this->dbSet->Filter($filterExpression);
        $data = $this->dbSet->AsArray();
        $result = array();
        $dataItemsCount = count($data);
        for ($i = 0; $i < $dataItemsCount; $i++) {
            $result[$i] = $data[$i]["ID"];
        }
        $this->assertEquals($values, $result);
    }
    /**
     * @dataProvider providerSort
     */
    public function testSort($sortExpression, $currentValue, $desc, $field) {
        $sorted = true;
        $this->dbSet->Sort($sortExpression);
        $data = $this->dbSet->AsArray();
        $dataItemsCount = count($data);
        for ($i = 0; $i < $dataItemsCount; $i++) {
            $compareResult = strcmp($currentValue, $data[$i][$field]);
            if ((!$desc && $compareResult > 0) || ($desc && $compareResult < 0)) {
                $sorted = false;
                break;
            }
            $currentValue = $data[$i][$field];
        }
        $this->assertTrue($sorted && $dataItemsCount > 0);
    }
    public function testSkipTake() {
        $this->dbSet->SkipTake(10, 5);
        $data = $this->dbSet->AsArray();
        $itemsCount = count($data);
        $firstIndex = $itemsCount > 0 ? $data[0]["ID"] : 0;
        $lastIndex = $itemsCount == 5 ? $data[4]["ID"] : 0;
        $this->assertTrue($itemsCount == 5 && $firstIndex == 11 && $lastIndex == 15);
    }
    /**
     * @dataProvider providerGroup
     */
    public function testGroup($groupExpression, $currentValue, $desc, $field, $groupCount) {
        $grouped = true;
        $this->dbSet->Group($groupExpression);
        $data = $this->dbSet->AsArray();
        $dataItemsCount = count($data);
        for ($i = 0; $i < $dataItemsCount; $i++) {
            $compareResult = strcmp($currentValue, $data[$i][$field]);
            if ((!$desc && $compareResult > 0) || ($desc && $compareResult < 0)) {
                $grouped = false;
                break;
            }
            $currentValue = $data[$i][$field];
        }
        $this->assertTrue($grouped && $dataItemsCount == $groupCount);
    }
    /**
     * @dataProvider providerGroupSummary
     */
    public function testGroupSummary($group, $groupSummary, $standard) {
        $this->dbSet->Group($group, $groupSummary);
        $data = $this->dbSet->AsArray();
        $result = $this->GroupSummariesEqual($data, $standard);
        $this->assertTrue($result);
    }
    /**
     * @dataProvider providerGetTotalSummary
     */
    public function testGetTotalSummary($summaryExpression, $value) {
        $data = $this->dbSet->GetTotalSummary($summaryExpression);
        $result = count($data) > 0 ? $data[0] : 0;
        $this->assertEquals($value, $result);
    }
    public function testGetGroupCount() {
        $groupExpression = array(
            (object)array(
                "selector" => "Category",
                "desc" => false
            )
        );
        $this->dbSet->Group($groupExpression);
        $groupCount = $this->dbSet->GetGroupCount();
        $this->assertEquals($groupCount, 4);
    }
}
