<?php
require_once(__DIR__.'/../more/DBViewUI.php');
require_once(__DIR__.'/../more/DBEditUI.php');
require_once(__DIR__.'/../more/DBDataUI.php');
require_once(__DIR__.'/../main/Icon.php');
require_once(__DIR__.'/../nubs/RSSItems.php');
require_once(__DIR__.'/../core/Converter.php');

class BriefRSSItemUI implements IObjectUI
{
    public function getStrUI($RSSItem)
    {
        $title = $RSSItem->getPropertyValue(RSSItem::Title);
        $text = $RSSItem->getPropertyValue(RSSItem::Text);
        $str = "<b>".$title."</b><br/>".$text."<br/>";
        return $str;
    }
}

// TODO : add a popup for content
class OneLineRSSItemUI implements IObjectUI
{
    public function getStrUI($RSSItem)
    {
        $title = $RSSItem->getPropertyValue(RSSItem::Title);
        $url = $RSSItem->getPropertyValue(RSSItem::SrcUrl);
        $text = $RSSItem->getPropertyValue(RSSItem::Text);
        if($url != "") {
            $str = "<a href='".$url."' target='_blank' title='".$text."'>".$title."</a><br/>";
        } else {
            $str = $title."<br/>";
            $str.= $text."<br/>";
        }
        return $str;
    }
}

class RSSItemsUIFactory
{
    public static function createViewUI($collection)
    {
        $ui = new DBViewUI();
        $ui->setCollection($collection);
        $ui->setObjectUI(new BriefRSSItemUI());
        $ui->setFilename($filename);
        return $ui;
    }
}

?>