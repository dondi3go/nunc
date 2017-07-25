<?php
require_once(__DIR__.'/../more/DBViewUI.php');
require_once(__DIR__.'/../more/DBEditUI.php');
require_once(__DIR__.'/../more/DBDataUI.php');
require_once(__DIR__.'/../main/Icon.php');
require_once(__DIR__.'/../nubs/Notes.php');

class BriefNoteUI implements IObjectUI
{
    public function getStrUI($note)
    {
        $str = "<b>".$note->getPropertyValue(Note::Title)."</b>";
        return $str;
    }
}

class NotesUIFactory
{
    public static function createViewUI($filename)
    {
        $ui = new DBViewUI();
        $ui->setCollection(new Notes());
        $ui->setObjectUI(new BriefNoteUI());
        $ui->setSeparator("<br/>\n");
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createEditUI($filename)
    {
        $ui = new DBEditUI();
        $ui->setCollection(new Notes());
        $ui->setObjectUI(new BriefNoteUI());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createDataUI($filename)
    {
        $ui = new DBDataUI();
        $ui->setCollection(new Notes());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createFullUI($filename)
    {
        $viewUI = NotesUIFactory::createViewUI($filename);
        $editUI = NotesUIFactory::createEditUI($filename);
        $dataUI = NotesUIFactory::createDataUI($filename);

        $ui = new MidNavBar();
        $ui->addLeftItem($viewUI, "view", Icon::EYE);
        $ui->addLeftItem($editUI, "edit", Icon::PEN);
        $ui->addRightItem($dataUI, "data", Icon::CLOUD);
        return $ui;
    }
}


?>