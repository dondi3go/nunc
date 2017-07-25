<?php

require_once(__DIR__.'/../core/FileSystem.php');
require_once(__DIR__.'/../main/IDisplayable.php');
require_once(__DIR__.'/../Config.php');

class NuncSecurityUI implements IDisplayable
{
    public function display(IPage $page)
    {
        $nuncRoot = __DIR__.'/..';

        $nuncRootUrl = FileSystem::convertPathToUrl($nuncRoot);
        $nuncConfigUrl = FileSystem::convertPathToUrl($nuncRoot.'/Config.php');

        $nuncDataStoreUrl = FileSystem::convertPathToUrl($nuncRoot.'/'.Config::data_store_folder);
        $nuncDataShareUrl = FileSystem::convertPathToUrl($nuncRoot.'/'.Config::data_share_folder);

        $str  = "<h3>Nunc Installation Security Check List</h3>";
        $str .= "<a href='".$nuncRootUrl."' target='_blank'>nunc root folder</a> : no dir (subdir /need/ should be accessible)<br/>\n";
        $str .= "<a href='".$nuncConfigUrl."' target='_blank'>nunc config file</a> : no access <br/>\n";
        $str .= "<a href='".$nuncDataStoreUrl."' target='_blank'>nunc data store folder</a> : no access <br/>\n";
        $str .= "<a href='".$nuncDataShareUrl."' target='_blank'>nunc data share folder</a> : no dir <br/>\n";

        $str .= "<br/>\n";
        $str .= ".htaccess suggestions : (todo)<br/>\n";

        $page->addBodyContent($str);
    }
}

class UserSecurityUI implements IDisplayable
{
    public function display(IPage $page)
    {
        $str  = "<h3>User Security</h3>";
        $str .= $_SERVER['REMOTE_ADDR']."<BR/>\n";
        $str .= $_SERVER['HTTP_USER_AGENT']."<BR/>\n";

        $str .= "Check <a href='https://panopticlick.eff.org/' target='_blanck'>fingerprinting</a><BR/>\n";

        $page->addBodyContent($str);
    }
}

class DomainSecurityUI implements IDisplayable
{
    public function display(IPage $page)
    {
        $domainName = $_SERVER['SERVER_NAME'];
        $str  = "<h3>Domain Security</h3>";
        $str .= $domainName."<BR/>\n";
        $str .= "Check if your name appears in the <a href='http://whois.domaintools.com/".$domainName."' target='_blank'>who is</a><BR/>\n";

        $page->addBodyContent($str);
    }
}


?>