<?php
require_once(__DIR__.'/../Config.php');
require_once(__DIR__.'/../core/Session.php');
require_once(__DIR__.'/IDisplayable.php');



//
// The most simple implementation of IDisplayable
//
class BasicDisplay implements IDisplayable
{
    private $strContent;

    function BasicDisplay($strContent)
    {
        $this->strContent = $strContent;
    }

    public function display(IPage $page)
    {
        $page->addBodyContent($this->strContent);
    }
}

//
// Stack several IDisplayable objects verticaly
//
class StackDisplay implements IDisplayable
{
    private $list = [];

    public function add(IDisplayable $displayable)
    {
        $this->list[] = $displayable;
    }

    public function display(IPage $page)
    {
        foreach($this->list as $displayable)
        {
            $displayable->display($page);
        }
    }
}

//
//
//
class ContainerDisplay implements IDisplayable
{
    private $prefix;
    private $suffix;
    private $displayable;

    public function ContainerDisplay($prefix, $suffix, IDisplayable $displayable)
    {
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->displayable = $displayable;
    }

    public function display(IPage $page)
    {
        $page->addBodyContent($this->prefix);
        $this->displayable->display($page);
        $page->addBodyContent($this->suffix);
    }
}

//
// Navigation bar
//
class BaseNavBar implements IDisplayable
{
    protected $leftItems = array(); // map of label->IDisplayble
    protected $leftIcons = array(); // map of label->icon

    protected $rightItems = array();
    protected $rightIcons = array();

    protected $strVarName = "BASE_NAV_BAR_ITEM";

    public function addLeftItem(IDisplayable $item, $strLabel, $strIcon='')
    {
        $this->leftItems[$strLabel] = $item;
        $this->leftIcons[$strLabel] = $strIcon;
    }

    public function addRightItem(IDisplayable $item, $strLabel, $strIcon='')
    {
        $this->rightItems[$strLabel] = $item;
        $this->rightIcons[$strLabel] = $strIcon;
    }

    public function display(IPage $page)
    {
        $this->handleUserAction();
        $this->addLibraries($page);
        $this->displayMenu($page);
        $this->displayItem($page);
    }

    protected function addLibraries(IPage $page)
    {
        // Add needed css and js libraries here
    }

    protected function displayMenu(IPage $page)
    {
        $strVarName = $this->strVarName;
        $str = "<form method='post'>\n";
        $strLabels = array_keys($this->leftItems);
        foreach($strLabels as $strLabel)
        {
            $str .= "<button name='".$strVarName."' value='".$strLabel."' type='submit'>";
            $str .= $strLabel;
            $str .= "</button>\n";
        }
        $strLabels = array_keys($this->rightItems);
        foreach($strLabels as $strLabel)
        {
            $str .= "<button name='".$strVarName."' value='".$strLabel."' type='submit'>";
            $str .= $strLabel;
            $str .= "</button>\n";
        }
        $str .= "</form>\n";
        $page->addBodyContent($str);
    }

    protected function displayItem(IPage $page)
    {
        $item = $this->getCurrentItem();
        
        if(isset($item))
        {
            $item->display($page);
        }
    }

    private function handleUserAction()
    {
        $strItemLabel = $_POST[$this->strVarName];
        if ((isset($strItemLabel)) && (!empty($strItemLabel)))
        {
            $this->setCurrentLabel($strItemLabel);
        }
    }

    private function setCurrentLabel($strLabel)
    {
        Session::setVariable($this->strVarName, $strLabel);
    }

    protected function getCurrentLabel()
    {
        $strLabel = Session::getVariable($this->strVarName);
        if ((isset($strLabel)) && (!empty($strLabel)))
        {
            // Check an item exists for this key
            // (important when changing page)
            if( isset($this->leftItems[$strLabel]) ) 
            {
                return $strLabel;
            }
            if( isset($this->rightItems[$strLabel]) ) 
            {
                return $strLabel;
            }
        }
        // Default is first item
        $strLabels = array_keys($this->leftItems);
        return $strLabels[0];
    }

    private function getCurrentItem()
    {
        $strLabel = $this->getCurrentLabel();
        
        $item = $this->leftItems[$strLabel];
        if(isset($item))
        {
            return $item;
        }

        $item = $this->rightItems[$strLabel];
        if(isset($item))
        {
            return $item;
        }
    }
}

//
// Bootstrap
//
class TopNavBar extends BaseNavBar
{
    function TopNavBar()
    {
        $this->strVarName = "TOP_NAV_BAR_ITEM";
    }

    function addLibraries(IPage $page)
    {
        $page->addCSS(Config::bootstrap_css);
        $page->addJS(Config::jquery_js);
        $page->addJS(Config::bootstrap_js); // to get mobile version of top nav bar
    }

    function displayMenu(IPage $page)
    {
        $strCurrentLabel = $this->getCurrentLabel();
        $str = "<form method='post'>\n";

        // Begin navbar
        $str .= "<nav class='navbar navbar-inverse navbar-fixed-top' role='navigation'>\n";
        $str .= "<div class='navbar-header'>\n";
        $str .= "    <button type='button' class='navbar-toggle' data-toggle='collapse' data-target='#navbar-collapse' aria-expanded='false'>\n";
        $str .= "        <span class='sr-only'>Toggle navigation</span>\n";
        $str .= "        <span class='icon-bar'></span>\n";
        $str .= "        <span class='icon-bar'></span>\n";
        $str .= "        <span class='icon-bar'></span>\n";
        $str .= "    </button>\n";
        //$str .= "<a class='navbar-brand' href='#''>Brand</a>\n";
        $str .= "</div>\n";


        $str .= "<div class='collapse navbar-collapse' id='navbar-collapse'>\n";

        // Left items
        $str .= "<ul class='nav navbar-nav pull-left'>\n";
        $str .= "    &nbsp;\n"; // so that first button does not stick to side
        $strLabels = array_keys($this->leftItems);
        foreach($strLabels as $strLabel)
        {
            $strClass = "btn navbar-btn btn-link";
            if($strLabel == $strCurrentLabel)
            {
                $strClass .= " active";
            }
            $strIcon = $this->leftIcons[$strLabel];
            $str .= "    <li>".$this->displayButton($strClass, $strLabel, $strIcon)."</li>\n";
        }
        $str .= "</ul>\n";
        
        // Right items
        $str .= "<ul class='nav navbar-nav pull-right'>\n";
        $strLabels = array_keys($this->rightItems);
        foreach($strLabels as $strLabel)
        {
            $strClass = "btn navbar-btn btn-link";
            if($strLabel == $strCurrentLabel)
            {
                $strClass .= " active";
            }
            $strIcon = $this->rightIcons[$strLabel];
            $str .= "    <li>".$this->displayButton($strClass, $strLabel, $strIcon)."</li>\n";
        }
        $str .= "    &nbsp;\n"; // so that last button does not stick to side
        $str .= "</ul>\n";

        $str .= "</div>\n";

        // End navbar
        $str .= "</nav>\n";
        $str .= "</form>\n";

        $page->addBodyContent($str);

        $page->addInnerCSSContent("body{padding-top:50px;}\n");
        $page->addInnerCSSContent(".btn-link:hover{text-decoration:none;}\n");
    }

    function displayButton($strClass, $strLabel, $strIcon)
    {
        $style = 2;
        $content = "content";
        if($style == 0) // label only
        {
            $content = $strLabel;
        }
        else if($style == 1) // icon only
        {
            $content = $strIcon;
        }
        else if($style == 2) // icon + label aside
        {
            $content = $strIcon." ".$strLabel;
        }
        else if($style == 3) // icon + label under
        {
            $content = $strIcon."<br/>".$strLabel;
        }
        $strPostVar = $this->strVarName;
        $str  = "<button class='".$strClass."' name='".$strPostVar."' value='".$strLabel."' type='submit'>";
        $str .= $content;
        $str .= "</button>";
        return $str;
    }
}

//
// Bootstrap
//
class MidNavBar extends BaseNavBar
{

    function MidNavBar()
    {
        $this->strVarName = "MID_NAV_BAR_ITEM";
    }

    function addLibraries(IPage $page)
    {
        $page->addCSS(Config::bootstrap_css);
    }

    function displayMenu(IPage $page)
    {
        $strCurrentLabel = $this->getCurrentLabel();
        $str = "<form method='post'>\n";

        // Left items
        $strLabels = array_keys($this->leftItems);
        $str .= "<div class='btn-group' role='group' aria-label='...'>\n";
        foreach($strLabels as $strLabel)
        {
            $strClass = "btn btn-default";
            if($strLabel == $strCurrentLabel)
            {
                $strClass .= " active";
            }
            $strIcon = $this->leftIcons[$strLabel];
            $str .= $this->displayButton($strClass, $strLabel, $strIcon)."\n";
        }
        $str .= "</div>\n";

        // Right items
        $strLabels = array_keys($this->rightItems);
        $str .= "<div class='btn-group pull-right' role='group' aria-label='...'>\n";
        foreach($strLabels as $strLabel)
        {
            $strClass = "btn btn-default";
            if($strLabel == $strCurrentLabel)
            {
                $strClass .= " active";
            }
            $strIcon = $this->rightIcons[$strLabel];
            $str .= $this->displayButton($strClass, $strLabel, $strIcon);
        }
        $str .= "</div>\n";
        $str .= "</form>\n";
        $str .="<br/>\n";

        $page->addBodyContent($str);
    }

    function displayButton($strClass, $strLabel, $strIcon)
    {
        $style = 3;
        $content = "content";
        if($style == 0) // label only
        {
            $content = $strLabel;
        }
        else if($style == 1) // icon only
        {
            $content = $strIcon;
        }
        else if($style == 2) // icon + label aside
        {
            $content = $strIcon." ".$strLabel;
        }
        else if($style == 3) // icon + label under
        {
            $content = $strIcon."<br/>".$strLabel;
        }
        $strPostVar = $this->strVarName;
        $str  = "<button class='".$strClass."' name='".$strPostVar."' value='".$strLabel."' type='submit'>";
        $str .= $content;
        $str .= "</button>";
        return $str;
    }
}

?>