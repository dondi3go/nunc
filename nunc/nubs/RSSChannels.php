<?php
require_once(__DIR__.'/../core/DBCollection.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../core/DBExporter.php');

class RSSChannel extends DBObject
{
    const Name    = "Name";
    const SrcUrl  = "SrcUrl";
    const SiteUrl = "SiteUrl";
    
    function RSSChannel()
    {
        $this->setTag("RSSChannel");
        $this->addProperty(Name, DBPropertyType::ShortText);
        $this->addProperty(SrcUrl, DBPropertyType::ShortText);
        $this->addProperty(SiteUrl, DBPropertyType::ShortText);
    }
}

class RSSChannels extends DBCollection
{
    function RSSChannels()
    {
        $this->setTag("RSSChannels");
    }

    protected function createObject()
    {
        $newObject = new RSSChannel();
        return $newObject;
    }
}

?>