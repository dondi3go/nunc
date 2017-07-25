<?php

//
// Top Menu Bar
// Only works with urls
// See Layout / TopNavBar to create something more fancy
//
class TopMenuBar implements IDisplayable
{
    protected $brandUrl;
    protected $brandLabel;

    protected $leftItems = array(); // map of label->url
    protected $leftIcons = array(); // map of label->icon

    protected $rightItems = array();
    protected $rightIcons = array();

    public function addBrand($strUrl, $strLabel)
    {
        $this->brandUrl = $strUrl;
        $this->brandLabel = $strLabel;
    }

    public function addLeftItem($strUrl, $strLabel, $strIcon='')
    {
        $this->leftItems[$strLabel] = $strUrl;
        $this->leftIcons[$strLabel] = $strIcon;
    }

    public function addRightItem($strUrl, $strLabel, $strIcon='')
    {
        $this->rightItems[$strLabel] = $strUrl;
        $this->rightIcons[$strLabel] = $strIcon;
    }

    public function display(IPage $page)
    {
        $this->displayMenu($page);
    }

    function displayMenu(IPage $page)
    {
        // Begin navbar
        $str .= "<nav class='navbar navbar-inverse navbar-fixed-top' role='navigation'>\n";
        $str .= "<div class='navbar-header'>\n";
        $str .= "    <button type='button' class='navbar-toggle' data-toggle='collapse' data-target='#navbar-collapse' aria-expanded='false'>\n";
        $str .= "        <span class='sr-only'>Toggle navigation</span>\n";
        $str .= "        <span class='icon-bar'></span>\n";
        $str .= "        <span class='icon-bar'></span>\n";
        $str .= "        <span class='icon-bar'></span>\n";
        $str .= "    </button>\n";
        if( !empty($this->brandLabel) )
        {
            $str .= "<a class='navbar-brand' href='".$this->brandUrl."'>".$this->brandLabel."</a>\n";
        }
        $str .= "</div>\n";

        $str .= "<div class='collapse navbar-collapse' id='navbar-collapse'>\n";

        // Left items
        $str .= "<ul class='nav navbar-nav pull-left'>\n";
        $str .= "    &nbsp;\n"; // so that first button does not stick to side
        $strLabels = array_keys($this->leftItems);
        foreach($strLabels as $strLabel)
        {
            $strUrl = $this->leftItems[$strLabel];
            $strIcon = $this->leftIcons[$strLabel];
            $str .= "    <li><a href='".$strUrl."'>".$strIcon.$strLabel."</a></li>\n";
        }
        $str .= "</ul>\n";
        
        // Right items
        $str .= "<ul class='nav navbar-nav pull-right'>\n";
        $strLabels = array_keys($this->rightItems);
        foreach($strLabels as $strLabel)
        {
            $strUrl = $this->rightItems[$strLabel];
            $strIcon = $this->rightIcons[$strLabel];
            $str .= "    <li><a href='".$strUrl."'>".$strIcon.$strLabel."</a></li>\n";
        }
        $str .= "    &nbsp;\n"; // so that last button does not stick to side
        $str .= "</ul>\n";

        $str .= "</div>\n";

        // End navbar
        $str .= "</nav>\n";

        $page->addBodyContent($str);

        $page->addInnerCSSContent("body{padding-top:50px;}\n");
    }

}

?>