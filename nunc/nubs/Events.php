<?php
require_once(__DIR__.'/../core/DBCollection.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../core/DBExporter.php');


//
//
//
class Event extends DBObject
{
	const Name   = "Name";
	const Day    = "Day";
	const Month  = "Month";
	const Note   = "Note";

	function Event()
	{
		$this->setTag("Event");
		$this->addProperty(Name);
		$this->addProperty(Day, DBPropertyType::IntNumber);
		$this->addProperty(Month, DBPropertyType::IntNumber);
		$this->addProperty(Note);
	}
}


//
//
//
class Events extends DBCollection
{
	function Events()
	{
		$this->setTag("Events");
	}

	protected function createObject()
	{
		return new Event();
	}
}

?>