<?php

require_once('DBProperty.php');
require_once('DBPropertyType.php');
require_once('DBPropertyChecker.php');

//
//
//
class DBObject
{
    private $strUid; // unique id (instance of object)
    private $strTag; // tag for XML and Code (class)
    
    private $properties = array(); // map of (tag, property)

    // Types of properties are stored outside properties
    // It saves memory, moreover, types are a bit optionnal (data are all string anyway)
    private $types      = array(); // map of (tag, type)

    // Enums are used for properties of type ENUM
    private $enums      = array(); // map of (tag, enum)

    function setTag($strTag)
    {
        $this->strTag = $strTag;
    }

    function setUid($strUid)
    {
        $this->strUid = $strUid;
    }

    function getUid()
    {
        return $this->strUid;
    }

    function addProperty($strTag, $strType = DBPropertyType::ShortText)
    {
        $property = new DBProperty();
        $property->setTag($strTag);
        $this->properties[$strTag] = $property;
        $this->types[$strTag] = $strType;
        return $property;
    }

    // --------------------------------------

    function setPropertyValue($strTag, $value)
    {
        $property = $this->properties[$strTag];
        if(isset($property))
        {
            $property->setValue($value);
            return true;
        }
        return false;
    }

    function getPropertyValue($strTag)
    {
        $property = $this->properties[$strTag];
        if(isset($property))
        {
            return $property->getValue();
        }
    }

    function getPropertyType($strTag)
    {
        return $this->types[$strTag];
    }

    // --------------------------------------

    function addEnum($strTag, $strEnum)
    {
        $this->enums[$strTag] = $strEnum;
    }

    function getEnum($strTag)
    {
        return $this->enums[$strTag];
    }

    // --------------------------------------

    // Returns false if one property is not good
    function checkProperties()
    {
        foreach ($this->properties as $property)
        {
            $strTag = $property->getTag();
            if( false == $this->checkProperty($strTag) )
            {
                return false;
            }
        }
        return true;
    }

    // Returns false if the property is not good
    function checkProperty($strTag)
    {
        $strValue = $this->getPropertyValue($strTag);
        $strType = $this->getPropertyType($strTag);
        return DBPropertyChecker::check($strValue, $strType);
    }

    // --------------------------------------   

    function getPropertyCount()
    {
        return count($this->properties);
    }

    function getPropertyByIndex($index)
    {
        if( $index < $this->getPropertyCount() )
        {
            $keys = array_keys($this->properties);
            return $this->properties[$keys[$index]];
        }
    }

    // --------------------------------------   

    /*// A Brief description of the object
    // Default is first property
    function getBrief()
    {
        $property = $this->GetPropertyByIndex(0);
        return $property->getValue();
    }

    // Details on the object
    // Default is nothing
    function getBriefDetails()
    {
    }*/

    // ------------------------------------------------------------

    function serialize()
    {
        $str = "";
        if(isset($this->strTag))
        {
            $str = "<".$this->strTag." uid='".$this->strUid."'>\n";
            foreach ($this->properties as $property)
            {
                $str = $str.$property->serialize();
            }
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

        if( !$this->strTag === $xml->getName() )
        {
            return false;
        }

        // Attributes

        foreach($xml->attributes() as $key => $value)
        {
            if($key === 'uid') {
                settype($value, "string");
                $this->setUid($value);
            }
        }

        // Properties

        $tagToPropertyMap = array();
        foreach ($this->properties as $property)
        {
            $tagToPropertyMap[$property->getTag()] = $property;
        }

        $bResult = true;
        foreach ($xml->children() as $element)
        {
            $property = $tagToPropertyMap[$element->getName()];
            if( isset($property) )
            {
                $bResult = $bResult && $property->deserialize($element);
            }
            else
            {
                echo "XML property ".$element->getName()." unknown. ";
                echo "Existing XML properties are : ";
                foreach ($this->properties as $property)
                {
                    echo $property->getTag()." ";
                }
                echo ".";
            }
        }
        return $bResult;
    }
}

?>