<?php

require_once(__DIR__.'/../more/DBViewUI.php');
require_once(__DIR__.'/../more/DBEditUI.php');
require_once(__DIR__.'/../more/DBDataUI.php');
require_once(__DIR__.'/../more/Picture.php');
require_once(__DIR__.'/../more/FileSystemUI.php');
require_once(__DIR__.'/../main/Icon.php');
require_once(__DIR__.'/../nubs/Blog.php');
require_once(__DIR__.'/../core/Converter.php');

class BriefPostUI implements IObjectUI
{
    public function getStrUI($post)
    {
        $str = "<b>".$post->getPropertyValue(Post::Title)."</b>";
        return $str;
    }
}

class FullPostUI implements IObjectUI
{
    private $picTagConverter;

    public function FullPostUI($dirpath)
    {
        $this->picTagConverter = new PicTagConverter();
        $this->picTagConverter->setRowTagPrefixSuffix("</p>", "<p class='text-justify'>");
        $this->picTagConverter->setPicBasePath($dirpath);
    }

    private function getTitle($post)
    {
        return $post->getPropertyValue(Post::Title);
    }

    private function getContent($post)
    {
        $str = $post->getPropertyValue(Post::Content);
        $str = Converter::newLineToBRTag($str);
        $str = Converter::BBToHTML($str);
        $str = $this->picTagConverter->getFullHTML($str);
        return $str;
    }

    public function getStrUI($post)
    {
        $str = "<h2>".$this->getTitle($post)."</h2>\n";
        $str.= "<p class='text-justify'>".$this->getContent($post)."</p>";
        return $str;
    }
}

class BlogUIFactory
{
    public static function createViewUI($filename, $dirpath = "")
    {
        $ui = new DBViewUI();
        $ui->setCollection(new Blog());
        $ui->setObjectUI(new FullPostUI($dirpath));
        $ui->setSeparator("<br/>\n<br/>\n<br/>\n");
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createEditUI($filename)
    {
        $ui = new DBEditUI();
        $ui->setCollection(new Blog());
        $ui->setObjectUI(new BriefPostUI());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createPicsUI($dirpath)
    {
        $ui = new FolderContentUI($dirpath);
        return $ui;
    }

    public static function createDataUI($filename)
    {
        $ui = new DBDataUI();
        $ui->setCollection(new Blog());
        $ui->setFilename($filename);
        return $ui;
    }

    // $filename = path of xml file
    // $dirpath = path of folder containing pictures
    public static function createFullUI($filename, $dirpath = "")
    {
        $blogUI = BlogUIFactory::createViewUI($filename, $dirpath);
        $prefix = "<div class='container'>\n<div class='col-md-offset-3 col-md-5'>\n";
        $suffix = "</div>\n</div>\n";

        $viewUI = new ContainerDisplay($prefix, $suffix, $blogUI);
        $editUI = BlogUIFactory::createEditUI($filename);
        $picsUI = BlogUIFactory::createPicsUI($dirpath);
        $dataUI = BlogUIFactory::createDataUI($filename);

        $ui = new MidNavBar();
        $ui->addLeftItem($viewUI, "view", Icon::EYE);
        $ui->addLeftItem($editUI, "edit", Icon::PEN);
        $ui->addLeftItem($picsUI, "pics", Icon::PICTURE);
        $ui->addRightItem($dataUI, "data", Icon::CLOUD);
        return $ui;
    }
}


?>