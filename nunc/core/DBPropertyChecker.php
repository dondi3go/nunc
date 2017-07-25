<?php

require_once('DBPropertyType.php');

//
//
//
class DBPropertyChecker
{
    public static function check($strValue, $strType)
    {
        switch($strType)
        {
            case DBPropertyType::FloatNumber:
                return self::checkFloatNumber($strValue);
                break;

            case DBPropertyType::IntNumber:
                return self::checkIntNumber($strValue);
                break;

            default:
                return true;
                break;
        }
    }

    private static function checkFloatNumber($strValue)
    {
        return is_numeric($strValue);
    }

    private static function checkIntNumber($strValue)
    {
        return ctype_digit($strValue);
    }
}

?>