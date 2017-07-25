<?php
require_once(__DIR__.'/../more/DBViewUI.php');
require_once(__DIR__.'/../more/DBEditUI.php');
require_once(__DIR__.'/../more/DBDataUI.php');
require_once(__DIR__.'/../main/Icon.php');
require_once(__DIR__.'/../nubs/Bookmarks.php');

class BriefBookmarkUI implements IObjectUI
{
    public function getStrUI($bookmark)
    {
        $title = $bookmark->getPropertyValue(Bookmark::Title);
        $srcUrl = $bookmark->getPropertyValue(Bookmark::SrcUrl);

        $str = "<b><a href='".$srcUrl."' target='blank'>".$title."</a></b><br/>";
        $str.= "<p class='text-muted small'>".$srcUrl."</p>";
        return $str;
    }
}


class BookmarksUIFactory
{
    public static function createViewUI($filename)
    {
        $ui = new DBViewUI();
        $ui->setCollection(new Bookmarks());
        $ui->setObjectUI(new BriefBookmarkUI());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createEditUI($filename)
    {
        $ui = new DBEditUI();
        $ui->setCollection(new Bookmarks());
        $ui->setObjectUI(new BriefBookmarkUI());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createDataUI($filename)
    {
        $ui = new DBDataUI();
        $ui->setCollection(new Bookmarks());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createFullUI($filename)
    {
        $viewUI = BookmarksUIFactory::createViewUI($filename);
        $editUI = BookmarksUIFactory::createEditUI($filename);
        $dataUI = BookmarksUIFactory::createDataUI($filename);

        $ui = new MidNavBar();
        $ui->addLeftItem($viewUI, "view", Icon::EYE);
        $ui->addLeftItem($editUI, "edit", Icon::PEN);
        $ui->addRightItem($dataUI, "data", Icon::CLOUD);
        return $ui;
    }
}

?>