<?php
namespace DevExtreme;
class Utils {
    public static function StringToNumber($str) {
        $currentLocale = localeconv();
        $decimalPoint = $currentLocale["decimal_point"];
        $result = strpos($str, $decimalPoint) === false ? intval($str) : floatval($str);
        return $result;
    }
    public static function EscapeExpressionValues($mySql, &$expression = NULL) {
        if (isset($expression)) {
            if (is_string($expression)) {
                $expression = $mySql->real_escape_string($expression);
            }
            else if (is_array($expression)) {
                foreach ($expression as &$arr_value) {
                    self::EscapeExpressionValues($mySql, $arr_value);
                }
                unset($arr_value);
            }
            else if (gettype($expression) === "object") {
                foreach ($expression as $prop => $value) {
                    self::EscapeExpressionValues($mySql, $expression->$prop);
                }
            }
        }
    }
    public static function QuoteStringValue($value, $isFieldName = true) {
        if (!$isFieldName) {
           $value = self::_ConvertDateTimeToMySQLValue($value);
        }
        
        if(is_null($value)){
            return $value;
        }
        
        $resultPattern = $isFieldName ? "`%s`" : (is_bool($value) ? "%s" : "'%s'");
        $result = sprintf($resultPattern, is_bool($value) ? ($value ? "1" : "0") : strval($value));
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
