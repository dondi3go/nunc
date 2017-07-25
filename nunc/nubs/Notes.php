<?php
require_once(__DIR__.'/../core/DBCollection.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../core/DBExporter.php');


//
//
//
class Note extends DBObject
{
    const Title   = "Title";
    const Content = "Content";

    function Note()
    {
        $this->setTag("Note");
        $this->addProperty(Title);
        $this->addProperty(Content, DBPropertyType::LongText);
    }
}


//
//
//
class Notes extends DBCollection
{
    function Notes()
    {
        $this->setTag("Notes");
    }

    protected function createObject()
    {
        return new Note();
    }
}

?>