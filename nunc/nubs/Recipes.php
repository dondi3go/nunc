<?php
require_once(__DIR__.'/../core/DBCollection.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../core/DBExporter.php');

class Recipe extends DBObject
{
    const Name        = "Name";
    const Ingredients = "Ingredients";
    const Directions  = "Directions";
    const Notes       = "Notes";
    
    function Recipe()
    {
        $this->setTag("Recipe");
        $this->addProperty(Name, DBPropertyType::ShortText);
        $this->addProperty(Ingredients, DBPropertyType::LongText);
        $this->addProperty(Directions, DBPropertyType::LongText);
        $this->addProperty(Notes, DBPropertyType::LongText);
    }
}

class Recipes extends DBCollection
{
    function Recipes()
    {
        $this->setTag("Recipes");
    }

    protected function createObject()
    {
        $newObject = new Recipe();
        $newObject->setPropertyValue(Name, "Nouvelle recette");
        return $newObject;
    }
}

?>