<?php
require_once(__DIR__.'/../more/DBViewUI.php');
require_once(__DIR__.'/../more/DBEditUI.php');
require_once(__DIR__.'/../more/DBDataUI.php');
require_once(__DIR__.'/../main/Icon.php');
require_once(__DIR__.'/../nubs/Quotes.php');
require_once(__DIR__.'/../core/Converter.php');

class BriefQuoteUI implements IObjectUI
{
    public function getStrUI($quote)
    {
        $author = $quote->getPropertyValue(Quote::Author);
        $str = "<b>".$author."</b>";
        return $str;
    }
}

class FullQuoteUI implements IObjectUI
{
    public function getStrUI($quote)
    {
        $text = $quote->getPropertyValue(Quote::Text);
        $text = Converter::newLineToBRTag($text);

        $author = $quote->getPropertyValue(Quote::Author);

        $str = "<i>".$text."</i><br/>";
        $str.= "<p class='text-muted'>".$author."</p>";
        return $str;
    }
}

class QuotesUIFactory
{
    public static function createViewUI($filename)
    {
        $ui = new DBViewUI();
        $ui->setCollection(new Quotes());
        $ui->setObjectUI(new FullQuoteUI());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createEditUI($filename)
    {
        $ui = new DBEditUI();
        $ui->setCollection(new Quotes());
        $ui->setObjectUI(new BriefQuoteUI());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createDataUI($filename)
    {
        $ui = new DBDataUI();
        $ui->setCollection(new Quotes());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createFullUI($filename)
    {
        $viewUI = QuotesUIFactory::createViewUI($filename);
        $editUI = QuotesUIFactory::createEditUI($filename);
        $dataUI = QuotesUIFactory::createDataUI($filename);

        $ui = new MidNavBar();
        $ui->addLeftItem($viewUI, "view", Icon::EYE);
        $ui->addLeftItem($editUI, "edit", Icon::PEN);
        $ui->addRightItem($dataUI, "data", Icon::CLOUD);
        return $ui;
    }
}

?>