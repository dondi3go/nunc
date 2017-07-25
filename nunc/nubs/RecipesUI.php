<?php
require_once(__DIR__.'/../more/DBViewUI.php');
require_once(__DIR__.'/../more/DBEditUI.php');
require_once(__DIR__.'/../more/DBDataUI.php');
require_once(__DIR__.'/../main/Icon.php');
require_once(__DIR__.'/../nubs/Recipes.php');
require_once(__DIR__.'/../core/Converter.php');

class BriefRecipeUI implements IObjectUI
{
    public function getStrUI($recipe)
    {
        $name = $recipe->getPropertyValue(Recipe::Name);
        $str = "<b>".$name."</b>";
        return $str;
    }
}

class FullRecipeUI implements IObjectUI
{
    public function getStrUI($recipe)
    {
        $name = $recipe->getPropertyValue(Recipe::Name);
        
        $ingredients = $recipe->getPropertyValue(Recipe::Ingredients);
        $ingredients = Converter::newLineToBRTag($ingredients);
        
        $directions = $recipe->getPropertyValue(Recipe::Directions);
        $directions = Converter::newLineToBRTag($directions);
        
        $notes = $recipe->getPropertyValue(Recipe::Notes);
        $notes = Converter::newLineToBRTag($notes);

        $str = "<b>".$name."</b><br/>";
        $str.= $ingredients."<br/>";
        $str.= $directions."<br/>";
        $str.= "<p class='text-muted'>".$notes."</p>";
        return $str;
    }
}


class RecipesUIFactory
{
    public static function createViewUI($filename)
    {
        $ui = new DBViewUI();
        $ui->setCollection(new Recipes());
        $ui->setObjectUI(new FullRecipeUI());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createEditUI($filename)
    {
        $ui = new DBEditUI();
        $ui->setCollection(new Recipes());
        $ui->setObjectUI(new BriefRecipeUI());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createDataUI($filename)
    {
        $ui = new DBDataUI();
        $ui->setCollection(new Recipes());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createFullUI($filename)
    {
        $viewUI = RecipesUIFactory::createViewUI($filename);
        $editUI = RecipesUIFactory::createEditUI($filename);
        $dataUI = RecipesUIFactory::createDataUI($filename);

        $ui = new MidNavBar();
        $ui->addLeftItem($viewUI, "view", Icon::EYE);
        $ui->addLeftItem($editUI, "edit", Icon::PEN);
        $ui->addRightItem($dataUI, "data", Icon::CLOUD);
        return $ui;
    }
}

?>