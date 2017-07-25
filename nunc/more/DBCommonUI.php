<?php
require_once(__DIR__.'/../core/Converter.php');

//
// Common interface for DBObject display in HTML page
//
interface IObjectUI
{
    // return a string : the HTML representation of the object
    public function getStrUI($object);
}


//
// Basic implementation of IObjectUI
//
class BasicObjectUI implements IObjectUI
{
    public function getStrUI($object)
    {
        $property0 = $object->getPropertyByIndex(0);
        $property1 = $object->getPropertyByIndex(1);
        $str = "<b>".$property0->getValue()."</b><br/>\n";
        $str.= $property1->getValue()."<br/>\n";
        return $str;
    }
}

//
// Implementation of IObjectUI : Properties name + value (View)
//
class PropertiesViewObjectUI implements IObjectUI
{
    public function getStrUI($object)
    {
        $str = "<table class='table table-condensed'>\n";
        for( $i=0; $i<$object->getPropertyCount(); $i++)
        {
            $property = $object->getPropertyByIndex($i);
            $strTag = $property->getTag();
            $strType  = $object->getPropertyType($strTag);
            $strValue = $property->getValue();
            $strFormattedValue = $this->formatValue($strValue, $strType);

            if(false == $object->checkProperty($strTag))
                $str.= "<tr class='danger'>\n";
            else
                $str.= "<tr>\n";
            $str.= "<td><p class='text-muted'>".$strTag."</p></td>\n";
            
            $str.= "<td>\n";
            switch($strType)
            {
                case DBPropertyType::ShortText:
                    $str.= "<input class='form-control' type='text' name='".$strName."' value='".$strFormattedValue."' readonly/>\n";
                    break;

                case DBPropertyType::MediumText:
                case DBPropertyType::Tags:
                    $str.= "<textarea class='form-control' rows='3' type='text' name='".$strName."' readonly>".$strFormattedValue."</textarea>\n";
                    break;

                case DBPropertyType::LongText:
                    $lineCount = substr_count($strFormattedValue, "\n") + 1;
                    $rows = max($lineCount, 10);
                    $str.= "<textarea class='form-control' rows='".$rows."' type='text' name='".$strName."' readonly>".$strFormattedValue."</textarea>\n";
                    break;

                default: // like ShortText, Float
                    $str.= "<input class='form-control' type='text' name='".$strName."' value='".$strFormattedValue."' readonly/>\n";
                    break;
            }
            $str.= "</td>\n";

            $str.= "</tr>\n";
        }
        $str.= "</table>";
        return $str;
    }

    private function formatValue($strValue, $strType)
    {
        $str = "";

        if($strType==DBPropertyType::DateTime)
        {
            $d = new DateTime($strValue);
            $str.= $d->format('Y-m-d H:i');
        }
        else if($strType==DBPropertyType::Tags)
        {
            if(strlen($strValue)>0)
            {
                $tagsAsArr = explode(",", $strValue);
                for( $j=0; $j<count($tagsAsArr); $j++ )
                {
                    $strTag = trim($tagsAsArr[$j]);
                    $str.= "#".$strTag." ";
                }
            }
        }
        else
        {
            $str .= Converter::HTMLEncode($strValue);
        }
        return $str;
    }
}

//
// Implementation of IObjectUI : Properties name + value (Edit)
// Each property is linked to an input whose name is prefix+propertyName
// Should be dipslay between <form></form> tags
//
class PropertiesEditObjectUI implements IObjectUI
{

    private $propertyPrefix = "";

    public function setPropertyPrefix($prefix)
    {
        $this->propertyPrefix = $prefix;
    }

    public function getStrUI($object)
    {
        $str = "<table class='table table-condensed'>\n";
        for( $i=0; $i<$object->getPropertyCount(); $i++)
        {
            $property = $object->getPropertyByIndex($i);
            $strTag = $property->getTag();
            $strType  = $object->getPropertyType($strTag);
            $strValue = $property->getValue();
            $strFormattedValue = $this->formatValue($strValue);
            $strName  = $this->propertyPrefix.$strTag;

            if(false == $object->checkProperty($strTag))
                $str.= "<tr class='danger'>\n";
            else
                $str.= "<tr>\n";
            $str.= "<td><p class='text-muted'>".$strTag."</p></td>\n";
            $str.= "<td>\n";
            switch($strType)
            {
                case DBPropertyType::ShortText:
                    $str.= "<input class='form-control' type='text' name='".$strName."' value='".$strFormattedValue."'></input>\n";
                    break;

                case DBPropertyType::MediumText:
                case DBPropertyType::Tags:
                    $str.= "<textarea class='form-control' rows='3' type='text' name='".$strName."'>".$strFormattedValue."</textarea>\n";
                    break;

                case DBPropertyType::LongText:
                    $lineCount = substr_count($strFormattedValue, "\n") + 1;
                    $rows = max($lineCount, 10);
                    $str.= "<textarea class='form-control' rows='".$rows."' type='text' name='".$strName."'>".$strFormattedValue."</textarea>\n";
                    $str.= "<p class='text-muted'>Special characters : À Ç È É Ê « » æ œ —</p>";
                    break;

                default: // like ShortText, Float
                    $str.= "<input class='form-control' type='text' name='".$strName."' value='".$strFormattedValue."'/>\n";
                    break;
            }
            $str.= "</td>\n";
            $str.= "</tr>\n";
        }
        $str.= "</table>";

        return $str;
    }

    private function formatValue($strValue)
    {
        return Converter::HTMLEncode($strValue);
    }
}


?>