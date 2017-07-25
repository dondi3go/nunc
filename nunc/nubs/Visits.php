<?php
require_once(__DIR__.'/../core/DBCollection.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../core/DBExporter.php');


//
//
//
class Visit extends DBObject
{
    const Ip          = "Ip";
    const Timestamp   = "Timestamp";
    const UserAgent   = "UserAgent";
    const Referer     = "Referer";
    const Note        = "Note";

    function Visit()
    {
        $this->setTag("Visit");
        $this->addProperty(Ip);
        $this->addProperty(Timestamp);
        $this->addProperty(UserAgent);
        $this->addProperty(Referer);
        $this->addProperty(Note);
    }

    function getCurrentData()
    {
        $this->setPropertyValue(Ip, $_SERVER['REMOTE_ADDR']);
        $this->setPropertyValue(Timestamp, time());
        $this->setPropertyValue(UserAgent, $_SERVER['HTTP_USER_AGENT']);
        $this->setPropertyValue(Referer,$_SERVER['HTTP_REFERER']);
    }
}


//
//
//
class Visits extends DBCollection
{
    function Visits()
    {
        $this->setTag("Visits");
    }

    protected function createObject()
    {
        return new Visit();
    }
}


//
//
//
class VisitLogger
{
    private $strDatabaseFilename;

    function VisitLogger($strDatabaseFilename)
    {
        $this->strDatabaseFilename = $strDatabaseFilename;
    }

    function logVisit($note)
    {
        $collection = new Visits();
        if( true == DBImporter::import($collection, $this->strDatabaseFilename) )
        {
            $newVisit = $collection->addObject();
            $newVisit->getCurrentData();
            $newVisit->setPropertyValue(Note, $note);

            DBExporter::export($collection, $this->strDatabaseFilename);
        }
    }
}
?>