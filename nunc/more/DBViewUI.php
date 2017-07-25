<?php

require_once(__DIR__.'/../core/FileSystem.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../main/IDisplayable.php');
require_once(__DIR__.'/../more/DBCommonUI.php');

//
//
//
class DBViewUI implements IDisplayable
{

    private $collection;
    private $filename;      // url of xml file
    private $objectUI;      // object displayer
    private $separator="";  // between each object display

    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function setObjectUI(IObjectUI $objectUI)
    {
        $this->objectUI = $objectUI;
    }

    public function setSeparator($str)
    {
        $this->separator = $str;
    }

    public function display(IPage $page)
    {
        $str = "";
        if( $this->loadDB() )
        {
            $str .= $this->displayCollection();
        }
        else
        {
            $str .= $this->displayMessage("no data");
        }
        $page->addBodyContent($str);
    }

    private function displayMessage($messageContent)
    {
        $str = "<div class='alert alert-success'>";
        $str.= $messageContent;
        $str.= "</div>\n";
        return $str;
    }

    private function displayCollection()
    {
        $col = new DBReverseCollection($this->collection); // newer first
        $str = "";
        $imax = $col->getObjectCount();
        for( $i=0; $i<$imax; $i++)
        {
            $object = $col->getObjectByIndex($i);
            $str.= $this->objectUI->getStrUI($object);
            $str.= $this->separator;
        }
        return $str;
    }

    private function loadDB()
    {
        if( isset($this->filename) )
        {
            if( Filesystem::fileExists($this->filename) )
            {
                return DBImporter::import($this->collection, $this->filename);
            }
        }
        return false;
    }
}

?>