<?php
use DevExtreme\AggregateHelper;

class AggregateHelperTest extends PHPUnit_Framework_TestCase {
    public function testGetFieldSetBySelectors() {
        $params = array(
            "field1",
            (object)array(
                "selector" => "field2"
            ),
            (object)array(
                "selector" => "field3",
                "desc" => true
            ),
            (object)array(
                "selector" => "field4",
                "groupInterval" => 10
            ),
            (object)array(
                "selector" => "field5",
                "groupInterval" => 10,
                "desc" => true
            ),
            (object)array(
                "selector" => "field6",
                "groupInterval" => "year",
            ),
            (object)array(
                "selector" => "field6",
                "groupInterval" => "month",
                "desc" => true
            )
        );
        $groupFields = "`field1`, `field2`, `field3`, `dx_field4_10`, `dx_field5_10`, `dx_field6_year`, `dx_field6_month`";
        $sortFields = "`field1`, `field2`, `field3` DESC, `dx_field4_10`, `dx_field5_10` DESC, `dx_field6_year`, `dx_field6_month` DESC";
        $selectFields = "`field1`, `field2`, `field3`, (`field4` - (`field4` % 10)) AS `dx_field4_10`, (`field5` - (`field5` % 10)) AS `dx_field5_10`, YEAR(`field6`) AS `dx_field6_year`, MONTH(`field6`) AS `dx_field6_month`";

        $fieldSet = AggregateHelper::GetFieldSetBySelectors($params);

        $this->assertEquals($groupFields, $fieldSet["group"]);
        $this->assertEquals($sortFields, $fieldSet["sort"]);
        $this->assertEquals($selectFields, $fieldSet["select"]);
    }
}
