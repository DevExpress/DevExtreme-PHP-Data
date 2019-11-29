<?php
use DevExtreme\FilterHelper;

class FilterHelperTest extends PHPUnit_Framework_TestCase {
    public function providerFilterExpression() {
        return array(
            array(
                array(
                    array("field1", "=", "Test"),
                    array("field2", "<", 3)
                ),
                "((`field1` = 'Test') AND (`field2` < '3'))"
            ),
            array(
                array(
                    array("field1", "=", "Test"),
                    "and",
                    array("field2", "<", 3)
                ),
                "((`field1` = 'Test') AND (`field2` < '3'))"
            ),
            array(
                array(
                    array("field1", "=", "Test"),
                    "or",
                    array("field2", "<", 3)
                ),
                "((`field1` = 'Test') OR (`field2` < '3'))"
            ),
            array(
                array(
                    array("field1", "=", "Test"),
                    "or",
                    array("field2", "<", 3)
                ),
                "((`field1` = 'Test') OR (`field2` < '3'))"
            ),
            array(
                array(
                    array("field1", "=", "Test"),
                    "and",
                    array(
                        "!",
                        array("field2", "<", 3)
                    )
                ),
                "((`field1` = 'Test') AND (NOT (`field2` < '3')))"
            ),
            array(
                array(
                    array("field1", "startswith", "test"),
                    "and",
                    array("field2", "endswith", "test")
                ),
                "((`field1` LIKE 'test%') AND (`field2` LIKE '%test'))"
            ),
            array(
                array(
                    array("field1", "contains", "test"),
                    "and",
                    array("field2", "notcontains", "test")
                ),
                "((`field1` LIKE '%test%') AND (`field2` NOT LIKE '%test%'))"
            )
        );
    }
    public function providerKey() {
        return array(
            array(
                array("field1" => 1),
                "`field1` = '1'"
            ),
            array(
                array(
                    "field1" => 1,
                    "field2" => 2
                ),
                "`field1` = '1' AND `field2` = '2'"
            )
        );
    }
    /**
     * @dataProvider providerFilterExpression
     */
    public function testGetSqlExprByArray($expression, $expectedResult) {
        $result = FilterHelper::GetSqlExprByArray($expression);

        $this->assertEquals($expectedResult, $result);
    }
    /**
     * @dataProvider providerKey
     */
    public function testGetSqlExprByKey($key, $expectedResult) {
        $result = FilterHelper::GetSqlExprByKey($key);

        $this->assertEquals($expectedResult, $result);
    }
}
