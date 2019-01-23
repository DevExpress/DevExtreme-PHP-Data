<?php
namespace DevExtreme;
class FilterHelper {
    private static $AND_OP = "AND";
    private static $OR_OP = "OR";
    private static $LIKE_OP = "LIKE";
    private static $NOT_OP = "NOT";
    private static function _GetSqlFieldName($field) {
        $fieldParts = explode(".", $field);
        $result = "";
        $fieldName = Utils::QuoteStringValue(trim($fieldParts[0]));
        if (count($fieldParts) == 2) {
            $dateProperty = trim($fieldParts[1]);
            $sqlDateFunction = "";
            $fieldPattern = "";
            switch ($dateProperty) {
                case "year":
                case "month":
                case "day": {
                    $sqlDateFunction = strtoupper($dateProperty);
                    $fieldPattern = "%s(%s)";
                    break;
                }
                case "dayOfWeek": {
                    $sqlDateFunction = strtoupper($dateProperty);
                    $fieldPattern = "%s(%s) - 1";
                    break;
                }
                default: {
                    throw new \Exception("The \"".$dateProperty."\" command is not supported");
                }
            }
            $result = sprintf($fieldPattern, $sqlDateFunction, $fieldName);
        }
        else {
            $result = $fieldName;
        }
        return $result;
    }
    private static function _GetSimpleSqlExpr($expression) {
        $result = "";
        $itemsCount = count($expression);
        $fieldName = self::_GetSqlFieldName(trim($expression[0]));
        if ($itemsCount == 2) {
            $val = $expression[1];
            $result = sprintf("%s = %s", $fieldName, Utils::QuoteStringValue($val, false));
        }
        else if ($itemsCount == 3) {
            $clause = trim($expression[1]);
            $val = $expression[2];
            $pattern = "";
            switch ($clause) {
                case "=":
                case "<>":
                case ">":
                case ">=":
                case "<":
                case "<=": {
                    $pattern = "%s %s %s";
                    $val = Utils::QuoteStringValue($val, false);
                    break;
                }
                case "startswith": {
                    $pattern = "%s %s '%s%%'";
                    $clause = self::$LIKE_OP;
                    $val = addcslashes($val, "%_");
                    break;
                }
                case "endswith": {
                    $pattern = "%s %s '%%%s'";
                    $val = addcslashes($val, "%_");
                    $clause = self::$LIKE_OP;
                    break;
                }
                case "contains": {
                    $pattern = "%s %s '%%%s%%'";
                    $val = addcslashes($val, "%_");
                    $clause = self::$LIKE_OP;
                    break;
                }
                case "notcontains": {
                    $pattern = "%s %s '%%%s%%'";
                    $val = addcslashes($val, "%_");
                    $clause = sprintf("%s %s", self::$NOT_OP, self::$LIKE_OP);
                    break;
                }
                default: {
                    $clause = "";
                }
            }
            
            if(is_null($val)){
                $val = "null";

                switch ($clause){
                    case "=":
                        $clause = "IS";
                        break;
                    case "<>":
                        $clause = "IS NOT";
                        break;
                }
            }
            
            $result = sprintf($pattern, $fieldName, $clause, $val);
        }
        return $result;
    }
    public static function GetSqlExprByArray($expression) {
        $result = "(";
        $prevItemWasArray = false;
        foreach ($expression as $index => $item) {
            if (is_string($item)) {
                $prevItemWasArray = false;
                if ($index == 0) {
				    if ($item == "!") {
                        $result .= sprintf("%s ", self::$NOT_OP);
						continue;
                    }
					$result .=  (isset($expression) && is_array($expression)) ? self::_GetSimpleSqlExpr($expression) : "";
					break;
                }
				$strItem = strtoupper(trim($item));
                if ($strItem == self::$AND_OP || $strItem == self::$OR_OP) {
                    $result .= sprintf(" %s ", $strItem);
                }
                continue;
            }
            if (is_array($item)) {
                if ($prevItemWasArray) {
                    $result .= sprintf(" %s ", self::$AND_OP);
                }
                $result .= self::GetSqlExprByArray($item);
                $prevItemWasArray = true;
            }
        }
        $result .= ")";
        return $result;
    }
    public static function GetSqlExprByKey($key) {
        $result = "";
        foreach ($key as $prop => $value) {
            $templ = strlen($result) == 0 ?
                     "%s = %s" :
                     " ".self::$AND_OP." %s = %s";
            $result .= sprintf($templ,
                               Utils::QuoteStringValue($prop),
                               Utils::QuoteStringValue($value, false));
        }
        return $result;
    }
}
