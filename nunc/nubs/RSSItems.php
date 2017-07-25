<?php
require_once(__DIR__.'/../core/DBCollection.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../core/DBExporter.php');

class RSSItem extends DBObject
{
    const Title      = "Title";
    const Author     = "Author";
    const Text       = "Text";
    const SrcUrl     = "SrcUrl";
    const ImgUrl     = "ImgUrl";
    
    function RSSItem()
    {
        $this->setTag("RSSItem");
        $this->addProperty(Title, DBPropertyType::ShortText);
        $this->addProperty(Author, DBPropertyType::ShortText);
        $this->addProperty(Text, DBPropertyType::LongText);
        $this->addProperty(SrcUrl, DBPropertyType::ShortText);
        $this->addProperty(ImgUrl, DBPropertyType::ShortText);
    }
}

class RSSItems extends DBCollection
{
    function RSSItems()
    {
        $this->setTag("RSSItems");
    }

    protected function createObject()
    {
        $newObject = new RSSItem();
        return $newObject;
    }
}

?>