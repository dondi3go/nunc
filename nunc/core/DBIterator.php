<?php

class DBIterator
{
    private $collection;
    private $nCurrentIndex;
    private $bReverseOrder;

    function DBIterator($collection, $bReverseOrder = false)
    {
        $this->collection = $collection;
        $this->bReverseOrder = $bReverseOrder;
        $this->nCurrentIndex = -1;
    }

    // Returns true or false
    function next()
    {
        $this->nCurrentIndex++;
        if($this->nCurrentIndex < $this->collection->getObjectCount())
        {
            return true;
        }
        return false;
    }

    // Returns an object of the collection
    function current()
    {
        $j = $this->nCurrentIndex;
        if($this->bReverseOrder == true)
        {
            $j = $this->collection->getObjectCount() -$j - 1;
        }
        return $this->collection->getObjectByIndex($j);
    }
}

?>