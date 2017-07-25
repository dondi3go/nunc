<?php

//
//
//
class Converter
{
    // Replace & < > ' " by 
    public static function XMLEncode($str)
    {
        $res = str_replace("&", "&amp;", $str);
        $res = str_replace("<", "&lt;", $res);
        $res = str_replace(">", "&gt;", $res);
        $res = str_replace('"', "&quot;", $res);
        $res = str_replace("'", "&#39;", $res);
        return $res;
    }

    // Replace 
    public static function XMLDecode($str)
    {
        $res = str_replace("&amp;", "&", $str);
        $res = str_replace("&lt;", "<", $res);
        $res = str_replace("&gt;", ">", $res);
        $res = str_replace("&quot;", '"', $res);
        $res = str_replace("&#39;", "'", $res);
        return $res;
    }

    // Property to TextArea (when editing)
    public static function HTMLEncode($str)
    {
        $str = str_replace("'", "&#39;", $str);
        $str = str_replace('"', "&quot;", $str);
        return $str;
    }

    // TextArea to Property (when editing)
    public static function HTMLDecode($str)
    {
        $str = str_replace("\'", "'", $str);
        $str = str_replace('\"', '"', $str);
        return $str;
    }

    // Clear HTML codes
    public static function HTMLClear($str)
    {
        $str = str_replace("&nbsp;", " ", $str);
        $str = str_replace("<b>",  "", $str);
        $str = str_replace("</b>", "", $str);
        $str = str_replace("<i>",  "", $str);
        $str = str_replace("</i>", "", $str);
        return $str;
    }

    // Replace new line by <br/>
    public static function newLineToBRTag($str)
    {
        return nl2br($str);
    }

    // Replace [URL][/URL] by corresponding HTML <a> tags
    public static function BBToHTML($str)
    {
        $search = array(
                '/\[b\](.*?)\[\/b\]/is',
                '/\[i\](.*?)\[\/i\]/is',
                '/\[u\](.*?)\[\/u\]/is',
                '/\[img\](.*?)\[\/img\]/is',
                '/\[url\](.*?)\[\/url\]/is',
                '/\[url\=(.*?)\](.*?)\[\/url\]/is'
                );

        $replace = array(
                '<strong>$1</strong>',
                '<em>$1</em>',
                '<u>$1</u>',
                '<img src="$1" />',
                '<a href="$1" target="_blank">$1</a>',
                '<a href="$1" target="_blank">$2</a>'
                );

        $str = preg_replace ($search, $replace, $str);
        return $str; 
    }
}

?>