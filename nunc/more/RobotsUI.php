<?php

require_once(__DIR__.'/../core/FileSystem.php');
require_once(__DIR__.'/../more/FileSystemUI.php');
require_once(__DIR__.'/../main/IDisplayable.php');
require_once(__DIR__.'/../Config.php');

class RobotsUI implements IDisplayable
{
    public function display(IPage $page)
    {
        $root = __DIR__.'/../../';

        $page->addBodyContent("<h3>robots.txt</h3>");

        // ROBOTS.TXT
        $page->addBodyContent("<br/>");
        $robotsPath = $root.'robots.txt';
        $ui1 = new TextFileContentUI($robotsPath);
        $ui1->display($page);

        // Comments
        $str = "<b>To Stay Unnoticed</b><br/>\n";
        $str.= "use this :\n";
        $str.= "<pre>User-agent: *\nDisallow: /</pre><br/>\n";

        $str.= "<b>To Be Popular</b><br/>\n";
        $str.= "use that :\n";
        $str.= "<pre>User-agent: *\nDisallow:</pre>\n";
        $page->addBodyContent($str);
    }
}

?>