<?php
namespace DevExtreme;
class Utils {
    public static function StringToNumber($str) {
        $currentLocale = localeconv();
        $decimalPoint = $currentLocale["decimal_point"];
        $result = strpos($str, $decimalPoint) === false ? intval($str) : floatval($str);
        return $result;
    }
    public static function QuoteStringValue($value, $isFieldName = true) {
        if (!$isFieldName) {
           $value = self::_ConvertDateTimeToMySQLValue($value);
        }
        $pattern = $isFieldName ? "/^\`.*\`$/" : "/^\'.*\'$/";
        $resultPattern = $isFieldName ? "`%s`" : "'%s'";
        $result = strval($value);
        if (preg_match($pattern, $result) != 1) {
            $result = sprintf($resultPattern, $result);
        }
        return $result;
    }
    public static function GetItemValueOrDefault($params, $key, $defaultValue = NULL) {
        return isset($params[$key]) ? $params[$key] : $defaultValue;
    }
    private static function _ConvertDatePartToISOValue($date) {
	    $dateParts = explode("/", $date);
	    return sprintf("%s-%s-%s", $dateParts[2], $dateParts[0], $dateParts[1]);
    }
    private static function _ConvertDateTimeToMySQLValue($strValue) {
	    $result = $strValue;
    	if (preg_match("/^\d{1,2}\/\d{1,2}\/\d{4}$/", $strValue) === 1) {
	    	$result = self::_ConvertDatePartToISOValue($strValue);
	    }
	    else if (preg_match("/^\d{1,2}\/\d{1,2}\/\d{4} \d{2}:\d{2}:\d{2}\.\d{3}$/", $strValue) === 1) {
		    $spacePos = strpos($strValue, " ");
            $datePart = substr($strValue, 0, $spacePos);		
		    $timePart = substr($strValue, $spacePos + 1);
		    $result = sprintf("%s %s", self::_ConvertDatePartToISOValue($datePart), $timePart);
	    }
	    return $result;
    } 
}