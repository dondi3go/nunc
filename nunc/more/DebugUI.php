<?php

require_once(__DIR__.'/../main/IDisplayable.php');

class DebugUI implements IDisplayable
{
    public function display(IPage $page)
    {
        $str  = "<h3>Session Variables</h3>\n";
        $strKeys = array_keys($_SESSION);
        foreach($strKeys as $strKey)
        {
            $str .= $strKey." = ".$_SESSION[$strKey]."<br/>\n";
        }

        $page->addBodyContent($str);
    }
}

?>