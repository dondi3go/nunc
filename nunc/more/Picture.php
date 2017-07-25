<?php
require_once(__DIR__.'/../main/IDisplayable.php');

//
//
//
class PicTagConverter
{
    private $strRowPrefix = "";
    private $strRowSuffix = "";

    private $strPicBasePath = "";

    public function setRowTagPrefixSuffix($strRowPrefix, $strRowSuffix)
    {
        $this->strRowPrefix = $strRowPrefix;
        $this->strRowSuffix = $strRowSuffix;
    }

    public function setPicBasePath($strPicBasePath)
    {
        $this->strPicBasePath = $strPicBasePath;
    }

    // replace tags [row] and [pic] by HTML tags
    public function getFullHTML($str)
    {
        $search = array(
            '/\[row\](.*?),(.*?)\[\/row\]/is',          // row : 2 elements
            '/\[pic\](.*?)\[\/pic\]/is'                 // pic
            );

        $strRowBegin = $this->strRowPrefix."<div class='row'>";
        $strRowEnd = "</div>".$this->strRowSuffix;
        $strBasePath = $this->strPicBasePath;

        $replace = array(
            $strRowBegin."<div class='col-md-6'>$1</div><div class='col-md-6'>$2</div>".$strRowEnd,
            "<img class='img-responsive center-block' src='".$strBasePath."$1' alt=''/>"
            );

        $result = preg_replace ($search, $replace, $str);
        return $result;
    }

    // remove tags [row] and [pic]
    public function getTextOnlyHTML($str)
    {
        $search = array(
            '/\[row\](.*?),(.*?)\[\/row\]/is',          // row : 2 elements
            '/\[pic\](.*?)\[\/pic\]/is'/*,                // pic
            '<br />\n<br />'*/                            // induced by disappearance of [row] [pic]
            );

        $replace = array(
            "",
            ""/*,
            "<br />"*/
            );

        $result = preg_replace ($search, $replace, $str);
        return $result;
    }
}

//
//
//
class Picture implements IDisplayable
{
    private $strUrl;
    private $srcAlt;

    function Picture($strUrl)
    {
        $this->strUrl = $strUrl;
    }

    public function display(IPage $page)
    {
        $str  = "<img class='img-responsive center-block' src='".$this->strUrl."' alt='".$this->srcAlt."'/>\n";
        $page->addBodyContent($str);
    }
}

?>