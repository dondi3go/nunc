<?php

//
// Property type is not stored within property, it's stored within object
// Property is 'type agnostic', its only a container
//
class DBProperty
{
    private $strValue; // always a string ! every other type will be converted
    private $strTag;

    function setTag($strTag)
    {
        $this->strTag = $strTag;
    }

    function getTag()
    {
        return $this->strTag;
    }

    function getValue()
    {
        return $this->strValue;
    }

    function setValue($value)
    {
        settype($value, "string");
        $this->strValue = $value;
    }

    // Replace & < > ' " by 
    function XMLEncode($str)
    {
        //$res = $str;
        $res = str_replace("&", "&amp;", $str);
        $res = str_replace("<", "&lt;", $res);
        $res = str_replace(">", "&gt;", $res);
        $res = str_replace('"', "&quot;", $res);
        $res = str_replace("'", "&#39;", $res);
        return $res;
    }

    // Replace 
    function XMLDecode($str)
    {
        //$res = $str;
        $res = str_replace("&amp;", "&", $str);
        $res = str_replace("&lt;", "<", $res);
        $res = str_replace("&gt;", ">", $res);
        $res = str_replace("&quot;", '"', $res);
        $res = str_replace("&#39;", "'", $res);
        return $res;
    }

    // Property to TextArea (when editing)
    static function HTMLEncode($str)
    {
        $str = str_replace("'", "&#39;", $str);
        $str = str_replace('"', "&quot;", $str);
        return $str;
    }

/*  // TextArea to Property (when editing)
    static function HTMLDecode($str)
    {
        $str = str_replace("\'", "'", $str);
        $str = str_replace('\"', '"', $str);
        return $str;
    }*/

    function serialize()
    {
        $str = "";
        if(isset($this->strTag))
        {
            $value = $this->XMLEncode($this->strValue); 

            $str = "<".$this->strTag.">";
            $str = $str.$value;
            $str = $str."</".$this->strTag.">\n";
        }
        return $str;
    }

    function deserialize($xml)
    {
        if (is_string($xml))
        {
            $xml = new SimpleXMLElement($xml);
        }

        if (!($xml instanceof SimpleXMLElement))
        {
            return false;
        }

        if(!$this->strTag === $xml->getName())
        {
            return false;
        }

        $value = $this->XMLDecode($xml);

        $this->setValue($value);

        return true;
    }
}
?>