<?php
ini_set('display_errors', 'On');
ini_set('error_reporting', 'E_ALL');

require_once(__DIR__.'/nunc/main/BasePage.php');
require_once(__DIR__.'/nunc/main/Layout.php');
require_once(__DIR__.'/nunc/more/TopMenuBar.php');
require_once(__DIR__.'/nunc/nubs/Visits.php');
require_once(__DIR__.'/nunc/nubs/BlogUI.php');
require_once(__DIR__.'/nunc/Config.php');

// DATA STORE FOLDER
$storeDir = Config::data_store_folder;
$shareDir = Config::data_share_folder;

// LOG VISIT
$visits = new VisitLogger( $storeDir."/visitsdb.xml" );
$visits->logVisit("index");

// TOP MENU BAR
$topmenubar = new TopMenuBar();
$topmenubar->addBrand('http://ericpain.fr/nunc', 'BLAH');
//$topmenubar->addLeftItem('http://lemonde.fr', 'lemonde');
//$topmenubar->addRightItem('http://lefigaro.fr', 'lefigaro');

// BLOG UI
$blogUI = BlogUIFactory::createViewUI( $storeDir."/blogdb.xml", $shareDir."/blog/" );

// CONTAINER UI
$prefix = "<div class='container'>\n";
$prefix.= "<div class='col-md-offset-3 col-md-5'>\n";
$prefix.= "<br/>\n";
$suffix = "</div>\n</div>\n";
$containerUI = new ContainerDisplay($prefix, $suffix, $blogUI);

// PAGE CONTENT
$page = new BasePage();
$page->enforceHTTPS();
$page->setLanguage('en');
$page->setTitle("BLAH");
$page->setIcon("favicon.ico");
$page->addCSS(Config::bootstrap_css);

$css = "body {"
        . "background-color: #DEDBD2;"
        . "color: #2D2E30;"
        . "text-rendering: optimizeLegibility;"
        . "font-family: 'Century Gothic', CenturyGothic, Geneva, sans-serif;}"
    . "bp, div {font-size: 105%;}";

$page->addInnerCSSContent($css);

$topmenubar->display($page);
$containerUI->display($page);

$page->flush();
?>