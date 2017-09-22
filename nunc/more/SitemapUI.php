<?php

require_once(__DIR__.'/../core/FileSystem.php');
require_once(__DIR__.'/../more/FileSystemUI.php');
require_once(__DIR__.'/../main/IDisplayable.php');
require_once(__DIR__.'/../Config.php');

//
// To manage the sitemap.xml file located at site root
// In order to have visits, or not to
//
class SitemapUI implements IDisplayable
{
    public function display(IPage $page)
    {
        $root = __DIR__.'/../../';

        $page->addBodyContent("<h3>sitemap.xml</h3>");

        // SITEMAP.XML
        $page->addBodyContent("<br/>");
        $sitemapPath = $root.'sitemap.xml';
        $ui2 = new TextFileContentUI($sitemapPath);
        $ui2->display($page);

        // Sample
$sampleSitemap = 
"<?xml version='1.0' encoding='UTF-8'?>
<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>
<url>
<loc>http://mydomain.com/</loc>
<lastmod>2017-09-21</lastmod>
<changefreq>daily</changefreq>
<priority>0.1</priority>
</url>
</urlset>";

        // Comments
        $str = "<b>To Stay Unnoticed</b><br/>\n";
        $str.= "no sitemap.xml file at all<br/><br/>\n";

        $str.= "<b>To Be Popular</b><br/>\n";
        $str.= "adapt this :\n";
        $str.= "<pre>".Converter::XMLEncode($sampleSitemap)."</pre>\n";
        $page->addBodyContent($str);
    }
}

?>