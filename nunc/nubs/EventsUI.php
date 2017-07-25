<?php
require_once(__DIR__.'/../more/DBViewUI.php');
require_once(__DIR__.'/../more/DBEditUI.php');
require_once(__DIR__.'/../more/DBDataUI.php');
require_once(__DIR__.'/../main/Icon.php');
require_once(__DIR__.'/../nubs/Events.php');

class BriefEventUI implements IObjectUI
{
    public function getStrUI($event)
    {
        $name  = $event->getPropertyValue(Event::Name);
        $day   = $event->getPropertyValue(Event::Day);
        $month = $event->getPropertyValue(Event::Month);
        $note =  $event->getPropertyValue(Event::Note);

        $str = "<b>".$name."</b><br/>";
        $str.= $day."/".$month."<br/>";
        return $str;
    }
}

class EventsUIFactory
{
    public static function createViewUI($filename)
    {
        $ui = new DBViewUI();
        $ui->setCollection(new Events());
        $ui->setObjectUI(new BriefEventUI());
        $ui->setSeparator("<br/>\n");
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createEditUI($filename)
    {
        $ui = new DBEditUI();
        $ui->setCollection(new Events());
        $ui->setObjectUI(new BriefEventUI());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createDataUI($filename)
    {
        $ui = new DBDataUI();
        $ui->setCollection(new Events());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createFullUI($filename)
    {
        $viewUI = EventsUIFactory::createViewUI($filename);
        $editUI = EventsUIFactory::createEditUI($filename);
        $dataUI = EventsUIFactory::createDataUI($filename);

        $ui = new MidNavBar();
        $ui->addLeftItem($viewUI, "view", Icon::EYE);
        $ui->addLeftItem($editUI, "edit", Icon::PEN);
        $ui->addRightItem($dataUI, "data", Icon::CLOUD);
        return $ui;
    }
}


?>