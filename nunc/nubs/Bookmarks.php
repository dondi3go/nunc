<?php
require_once(__DIR__.'/../core/DBCollection.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../core/DBExporter.php');

class Bookmark extends DBObject
{
    const Title   = "Title";
    const SrcUrl  = "SrcUrl";
    // Add tags
    // Add srcico
    
    function Bookmark()
    {
        $this->setTag("Bookmark");
        $this->addProperty(Title);
        $this->addProperty(SrcUrl);
    }
}

class Bookmarks extends DBCollection
{
    function Bookmarks()
    {
        $this->setTag("Bookmarks");
    }

    protected function createObject()
    {
        $newObject = new Bookmark();
        $newObject->setPropertyValue(Title, "Site Name");
        return $newObject;
    }
}

?>