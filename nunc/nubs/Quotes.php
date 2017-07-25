<?php
require_once(__DIR__.'/../core/DBCollection.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../core/DBExporter.php');

class Quote extends DBObject
{
    const Text       = "Text";
    const Author     = "Author";
    const References = "References";
    const Notes      = "Notes";
    
    function Quote()
    {
        $this->setTag("Quote");
        $this->addProperty(Text, DBPropertyType::LongText);
        $this->addProperty(Author, DBPropertyType::ShortText);
        $this->addProperty(References, DBPropertyType::ShortText);
        $this->addProperty(Notes, DBPropertyType::LongText);
    }
}

class Quotes extends DBCollection
{
    function Quotes()
    {
        $this->setTag("Quotes");
    }

    protected function createObject()
    {
        $newObject = new Quote();
        $newObject->setPropertyValue(Text, "New quote");
        return $newObject;
    }
}

?>