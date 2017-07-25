<?php

require_once(__DIR__.'/../core/FileSystem.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../main/IDisplayable.php');
require_once(__DIR__.'/../main/Icon.php');
require_once(__DIR__.'/../more/DBCommonUI.php');

interface DBDataAction
{
    const CREATE       = 'dbdata_create';
    const DOWNLOAD     = 'dbdata_download'; // xml
    const UPLOAD       = 'dbdata_upload';
}

//
//
//
class DBDataUI implements IDisplayable
{

    private $collection;
    private $filename;      // url of xml file
    private $loadtime;

    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function display(IPage $page)
    {
        $filebase = FileSystem::getFileBasename($this->filename);
        $filesize = FileSystem::getFileSizeAsString($this->filename);

        $str = "";
        if( $this->loadDB() )
        {
            $count = $this->collection->getObjectCount();

            $str.= $this->diplayMessage("<b>".$filebase."</b> loaded in <b>".$this->loadtime."</b> seconds");

            $str.= "<form method='post'><div>\n";

            // upload
            $str.= "<button class='btn btn-primary pull-right' name='".DBDataAction::UPLOAD."' type='submit' value='novalue'>\n";
            $str.= "    ".Icon::CLOUD_UP." upload\n";
            $str.= "</button> \n";

            // download
            $str.= "<button class='btn btn-primary pull-left' name='".DBDataAction::DOWNLOAD."' type='submit' value='novalue'>\n";
            $str.= "    ".Icon::CLOUD_DOWN." download\n";
            $str.= "</button> \n";

            $str.= "</div></form>\n";

            $str.= "<br/><br/>\n";

            // table
            $str.= "<table class='table table-condensed'>\n";
            $str.= "<tr><td><p class='text-muted'>items</p></td><td><b>".$count."</b></td>\n";
            $str.= "<tr><td><p class='text-muted'>size</p></td><td><b>".$filesize."</b></td>\n";
            $str.= "</table>\n";

        }
        else if( !isset($this->filename) )
        {
            $str.= $this->diplayMessage("database name not properly set");
        }
        else if( !Filesystem::fileExists($this->filename) )
        {
            $str.= $this->diplayMessage("database <b>".$filebase."</b> not found");

            // create
            $str.= "<button class='btn btn-primary pull-left' name='".DBDataAction::CREATE."' type='submit' value='novalue'>\n";
            $str.= "    <span class='glyphicon glyphicon-cloud'></span> create\n";
            $str.= "</button><br/><br/>\n";
        }
        else
        {
            $str.= $this->diplayMessage("error while reading database");
        }
        $page->addBodyContent($str);
    }

    private function diplayMessage($strMessage)
    {
        $str = "<div class='alert alert-success'>";
        $str.= $strMessage;
        $str.= "</div>\n";
        return $str;
    }

    private function loadDB()
    {
        if( isset($this->filename) )
        {
            if( Filesystem::fileExists($this->filename) )
            {
                $timeStart = microtime(true);
                $result = DBImporter::import($this->collection, $this->filename);
                $timeEnd = microtime(true);
                $this->loadtime = round($timeEnd - $timeStart, 4);
                return $result;
            }
        }
        return false;
    }
}

?>