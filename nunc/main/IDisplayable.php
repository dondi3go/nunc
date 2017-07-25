<?php
require_once('IPage.php');

//
// Common interface for anything displayed on a page
//
interface IDisplayable
{
    public function display(IPage $page);
}

?>