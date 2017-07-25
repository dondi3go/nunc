<?php
require_once(__DIR__.'/../more/DBViewUI.php');
require_once(__DIR__.'/../more/DBEditUI.php');
require_once(__DIR__.'/../more/DBDataUI.php');
require_once(__DIR__.'/../main/Icon.php');
require_once(__DIR__.'/../nubs/RSSChannels.php');
require_once(__DIR__.'/../nubs/RSSItemsUI.php');
require_once(__DIR__.'/../nubs/RSSReaderUI.php');
require_once(__DIR__.'/../core/Converter.php');

class BriefRSSChannelUI implements IObjectUI
{
    public function getStrUI($RSSChannel)
    {
        $name = Converter::HTMLEncode( $RSSChannel->getPropertyValue(RSSChannel::Name) );
        $srcUrl = $RSSChannel->getPropertyValue(RSSChannel::SrcUrl);
        $siteUrl = $RSSChannel->getPropertyValue(RSSChannel::SiteUrl);
        $str = "<a href='".$siteUrl."' target='_blank'><b>".$name."</b></a><br/>";
        $str.= "<p class='text-muted small'>".$srcUrl."</p>";
        return $str;
    }
}

class RSSChannelsUIFactory
{
    // Get items from channels
    public static function createViewUI($filename)
    {
        $ui = new RSSreaderUI();
        $ui->setRSSChannelsFilename($filename);
        $ui->setRSSItemUI(new OneLineRSSItemUI());
        return $ui;
    }

    public static function createCheckUI($filename)
    {
        $ui = new RSSreaderUI();
        $ui->setRSSChannelsFilename($filename);
        $ui->setRSSItemUI(new BriefRSSItemUI());
        $ui->setDebugMode(true);
        return $ui;
    }

    public static function createEditUI($filename)
    {
        $ui = new DBEditUI();
        $ui->setCollection(new RSSChannels());
        $ui->setObjectUI(new BriefRSSChannelUI());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createDataUI($filename)
    {
        $ui = new DBDataUI();
        $ui->setCollection(new RSSChannels());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createFullUI($filename)
    {
        $viewUI = RSSChannelsUIFactory::createViewUI($filename);
        $checkUI = RSSChannelsUIFactory::createCheckUI($filename);
        $editUI = RSSChannelsUIFactory::createEditUI($filename);
        $dataUI = RSSChannelsUIFactory::createDataUI($filename);

        $ui = new MidNavBar();
        $ui->addLeftItem($viewUI, "view", Icon::EYE);
        $ui->addLeftItem($checkUI, "check", Icon::BUG);
        $ui->addLeftItem($editUI, "edit", Icon::PEN);
        $ui->addRightItem($dataUI, "data", Icon::CLOUD);
        return $ui;
    }
}

?>