<?php
require_once("TestBase.php");
use DevExtreme\Utils;

class UtilsTest extends TestBase {
    public function providerValue() {
        return array(
            array(1, false, "'1'"),
            array("field", true, "`field`"),
            array(false, false, "0"),
            array(true, false, "1"),
            array(NULL, false, "NULL"),
        );
    }
    public function providerItemValue() {
        return array(
            array(
                array("field" => 1),
                "field",
                NULL,
                1
            ),
            array(
                array("field" => 1),
                "field1",
                "test",
                "test"
            )
        );
    }
    public function testEscapeExpressionValues() {
        $result = "tes't";
        Utils::EscapeExpressionValues(UtilsTest::$mySQL, $result);

        $this->assertEquals("tes\'t", $result);
    }
    /**
     * @dataProvider providerValue
     */
    public function testQuoteStringValue($value, $isFieldName, $expectedResult) {
        $result = Utils::QuoteStringValue($value, $isFieldName);

        $this->assertEquals($expectedResult, $result);
    }
    /**
     * @dataProvider providerItemValue
     */
    public function testGetItemValueOrDefault($params, $key, $defaultValue, $expectedResult) {
        $result = Utils::GetItemValueOrDefault($params, $key, $defaultValue);

        $this->assertEquals($expectedResult, $result);
    }
}
