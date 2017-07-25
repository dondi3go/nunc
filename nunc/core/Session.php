<?php

//
// Session management
//
class Session
{
    static public function setVariable($strKey, $strValue)
    {
        $_SESSION[$strKey] = $strValue;
    }

    static public function getVariable($strKey)
    {
        return $_SESSION[$strKey];
    }

    static public function removeVariable($strKey)
    {
        unset( $_SESSION[$strKey] );
    }

    static public function removeAllVariables()
    {
        $strKeys = array_keys($_SESSION);
        foreach($strKeys as $strKey)
        {
            Session::removeVariable($strKey);
        }
    }
}

?>