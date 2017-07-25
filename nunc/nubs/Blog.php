<?php
require_once(__DIR__.'/../core/DBCollection.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../core/DBExporter.php');


//
//
//
class Post extends DBObject
{
    const Title   = "Title";
    const Content = "Content";

    function Post()
    {
        $this->setTag("Post");
        $this->addProperty(Title);
        $this->addProperty(Content, DBPropertyType::LongText);
    }
}


//
//
//
class Blog extends DBCollection
{
    function Blog()
    {
        $this->setTag("Blog");
    }

    protected function createObject()
    {
        return new Post();
    }
}

?>