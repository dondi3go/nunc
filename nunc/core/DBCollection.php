<?php
require_once('DBObject.php');


//
//
//
interface IDBCollection
{
    public function getObjectCount();
    public function getObjectByIndex($index);
}

//
//
//
class DBReverseCollection implements IDBCollection
{
    private $collection;

    public function DBReverseCollection(IDBCollection $collection)
    {
        $this->collection = $collection;
    }

    public function getObjectCount()
    {
        return $this->collection->getObjectCount();
    }

    public function getObjectByIndex($index)
    {
        $newIndex = $this->collection->getObjectCount()-1-$index;
        return $this->collection->getObjectByIndex($newIndex);
    }
}


//
//
//
class DBCollection implements IDBCollection
{
	private $strTag;
	private $objects = array(); // map of (uid, object)

	function setTag($strTag)
	{
		$this->strTag = $strTag;
	}

	// Override me !
	protected function createObject()
	{
		$object = new DBObject();
		$object->setTag("ObjectTag");
		$object->addProperty("PropertyTag");
		return $object;
	}
	
	function addObject()
	{
		$object = $this->createObject();
		$uid = uniqid();
		$object->setUid( $uid );
		$this->objects[$uid] = $object;
		return $object;
	}

	function removeObject($object)
	{
		if(isset($object))
		{
			$uid = $object->getUid();
			unset($this->objects[$uid]);
		}
	}

	function getObjectCount()
	{
		return count($this->objects);
	}

	function getObjectByIndex($index)
	{
		if($index < $this->getObjectCount())
		{
			$keys = array_keys($this->objects);
			return $this->objects[$keys[$index]];
		}
	}

	function getObjectByUid($uid)
	{
		return $this->objects[$uid];
	}

	// Return a string containing XML tags
	function serialize()
	{
		$str = "";
		if(isset($this->strTag))
		{
			$str = "<".$this->strTag.">\n";
			foreach ($this->objects as $object)
			{
				$str = $str.$object->serialize();
			}
			$str = $str."</".$this->strTag.">\n";
		}
		return $str;
	}

	// $xml can be a string or a SimpleXmlElement
	// Return true or false
	function deserialize($xml)
	{
		if (is_string($xml))
		{
			try
			{
				$xml = @new SimpleXMLElement($xml);	// mute constructor errors
			}
			catch(Exception $e)
			{
				return false;
			}
		}

		if (!($xml instanceof SimpleXMLElement))
		{
			return false;
		}

		if( !$this->strTag === $xml->getName() )
		{
			return false;
		}

		$bResult = true;
		foreach ($xml->children() as $element)
		{
			$object = $this->createObject();
			if(!$object->deserialize($element))
			{
				$bResult = false;
			}
			else
			{
				$uid = $object->getUid();
				if( isset($uid) )
				{
					$this->objects[$uid] = $object;
				}
			}
		}
		return $bResult;
	}
	
}

?>