<?php

require_once(__DIR__.'/../core/FileSystem.php');
require_once(__DIR__.'/../more/FileSystemUI.php');
require_once(__DIR__.'/../main/IDisplayable.php');
require_once(__DIR__.'/../Config.php');

class SeoUI implements IDisplayable
{
    public function display(IPage $page)
    {
        $root = __DIR__.'/../../';

        $page->addBodyContent("<h3>Search Engine Optimization</h3>");

        // ROBOTS.TXT
        $page->addBodyContent("<br/>");
        $robotsPath = $root.'robots.txt';
        $ui1 = new TextFileContentUI($robotsPath);
        $ui1->display($page);

        // SITEMAP.XML
        $page->addBodyContent("<br/>");
        $sitemapPath = $root.'sitemap.xml';
        $ui2 = new TextFileContentUI($sitemapPath);
        $ui2->display($page);

        // Check HTML 5 semantic
        //$page->addBodyContent("check HTML 5 semantic : https://gsnedders.html5.org/outliner/");
    }
}

?>