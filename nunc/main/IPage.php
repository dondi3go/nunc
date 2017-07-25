<?php

//
// Common interface for web page (from components points of view)
//
interface IPage
{
    public function addCSS($strFileUrl);
    public function addJS($strFileUrl);
    public function addInnerCSSContent($str);
    public function addBodyContent($str);
}

?>