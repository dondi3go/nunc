<?php

require_once(__DIR__.'/../core/FileSystem.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../main/IDisplayable.php');
require_once(__DIR__.'/../more/DBCommonUI.php');
require_once(__DIR__.'/../nubs/RSSReader.php');

//
// Have several modes : oneline (sorted or not), with pic, pic only
//
class RSSReaderUI implements IDisplayable
{
    private $RSSChannels;
    private $RSSchannelsFilename;  // url of xml file
    private $RSSItemUI;            // object displayer
    private $separator = "";       // between each object display
    private $debugMode = false;

    public function RSSReaderUI()
    {
        $this->RSSChannels = new RSSChannels();
        $this->RSSItems = new RSSItems();
    }

    public function setRSSChannelsFilename($filename)
    {
        $this->RSSchannelsFilename = $filename;
    }

    public function setRSSItemUI(IObjectUI $RSSItemUI)
    {
        $this->RSSItemUI = $RSSItemUI;
    }

    public function setDebugMode($debugMode)
    {
        $this->debugMode = $debugMode;
    }

    // IDisplayable implementation
    public function display(IPage $page)
    {
        $str = "";
        if( $this->loadDB() )
        {
            // Convert Channels to Items
            $items = new RSSItems();
            $reader = new RSSReader();
            if($this->debugMode) {
                $reader->checkChannels($this->RSSChannels, $items);
            } else {
                $reader->readChannels($this->RSSChannels, $items);
            }

            $str .= $this->displayItems($items);
        }
        else
        {
            $str .= $this->displayMessage("no data");
        }
        $page->addBodyContent($str);
    }

    private function displayItems($RSSItems)
    {
        $imax = $RSSItems->getObjectCount();
        for( $i=0; $i<$imax; $i++)
        {
            $item = $RSSItems->getObjectByIndex($i);
            $str.= $this->RSSItemUI->getStrUI($item);
            $str.= $this->separator;
        }
        return $str;
    }

    private function loadDB()
    {
        if( isset($this->RSSchannelsFilename) )
        {
            if( Filesystem::fileExists($this->RSSchannelsFilename) )
            {
                return DBImporter::import($this->RSSChannels, $this->RSSchannelsFilename);
            }
        }
        return false;
    }
}

?>
