<?
require_once(__DIR__.'/../core/DBCollection.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../core/DBExporter.php');

class Place extends DBObject
{
    const Name        = "Name";
    const Longitude   = "Longitude";
    const Latitude    = "Latitude";
    const Address     = "Address";
    const Note        = "Note";
    const Url         = "Url";

    function Place()
    {
        $this->setTag("Place");
        $this->addProperty(Name);
        $this->addProperty(Latitude, DBPropertyType::FloatNumber);
        $this->addProperty(Longitude, DBPropertyType::FloatNumber);
        $this->addProperty(Address, DBPropertyType::MediumText);
        $this->addProperty(Note, DBPropertyType::MediumText);
        $this->addProperty(Url, DBPropertyType::ShortText);
    }
}

class Places extends DBCollection
{
    function Places()
    {
        $this->setTag("Places");
    }

    protected function createObject()
    {
        $newObject = new Place();
        $newObject->setPropertyValue(Latitude, 48.841);
        $newObject->setPropertyValue(Longitude, 2.302);
        return $newObject;
    }
}

/*
class GeoMarkersBrowser extends DBBrowser
{
	protected function afterDisplayViewObject($object)
	{
		$this->displayObjectOnMap($object);
	}

	protected function afterDisplayEditObject($object)
	{
		$this->displayObjectOnMap($object);
	}

	protected function displayObjectOnMap($object)
	{
		$lat = $object->getPropertyValue(GeoMarker::Latitude);
		$lon = $object->getPropertyValue(GeoMarker::Longitude);
		$content = $object->getTooltipContent();

		$map = new Map();
		$map->start("100%", "200px", $lat, $lon,  18);
		$map->showCoordinates();
		// Use the icon matching the category, not the default one
		$map->addLayer("Default", "glyphicon-asterisk", "blue");
		$map->addMarker($lat, $lon, $content);
		$map->stop();

		echo "<br/>";
	}
}

class GeoMarkersPlugin
{
	private $strDatabaseFilename;
	private $geoLayers;

	function GeoMarkersPlugin($strDatabaseFilename)
	{
		$this->strDatabaseFilename = $strDatabaseFilename;
	}

	function setGeolayers($strDatabaseFilename)
	{
		$data = loadFile($strDatabaseFilename);
		$this->geoLayers = new GeoLayers();
		$this->geoLayers->deserialize($data);
	}

	// Exemple : "100%", "400px", 48.841, 2.302,  13
	function display($strWidth, $strHeight, $dLati, $dLongi, $iZoom)
	{
		$map = new Map();
		$map->setSearchLabel("Rechercher une adresse ...");
		$map->setAddressUnknown("Désolé, adresse inconnue ...");
		$map->start($strWidth, $strHeight, $dLati, $dLongi, $iZoom); // Paris
		
		$data = loadFile($this->strDatabaseFilename);
		$collection = new GeoMarkers();
		$collection->deserialize($data);
		
		if(!isset($this->geoLayers))
		{
			// Without layers definition 
			$map->addLayer("Default", "glyphicon-asterisk", "blue");
			for( $i=0; $i<$collection->getObjectCount(); $i++)
			{
				$object = $collection->getObjectByIndex($i);
				
				$lat = $object->getPropertyValue(GeoMarker::Latitude);
				$lon = $object->getPropertyValue(GeoMarker::Longitude);
				$content = $object->getTooltipContent();
				$map->addMarker($lat, $lon, $content);
			}
		}
		else
		{
			// With layers definition
			for( $j=0; $j<$this->geoLayers->getObjectCount(); $j++)
			{
				$layer = $this->geoLayers->getObjectByIndex($j);
				$layerCode = $layer->getPropertyValue(Geolayer::Code);
				$map->addLayer(
					$layer->getPropertyValue(Geolayer::Name),
					$layer->getPropertyValue(Geolayer::Icon),
					$layer->getPropertyValue(Geolayer::Color));

				for( $i=0; $i<$collection->getObjectCount(); $i++)
				{
					$object = $collection->getObjectByIndex($i);
					$objectCategory = $object->getPropertyValue(GeoMarker::Category);
					
					if($layerCode == $objectCategory)
					{
						$lat = $object->getPropertyValue(GeoMarker::Latitude);
						$lon = $object->getPropertyValue(GeoMarker::Longitude);
						$content = $object->getTooltipContent();
						$map->addMarker($lat, $lon, $content);
					} 
				}
			}
		}

		$map->stop();
	}
}

class GeoMarkersAdminPlugin implements IDisplayPlugin
{
	private $strDatabaseFilename;

	function GeoMarkersAdminPlugin($strDatabaseFilename)
	{
		$this->strDatabaseFilename = $strDatabaseFilename;
	}

	function display()
	{
		$browser = new GeoMarkersBrowser();
		$browser->setCollection( new GeoMarkers() );
		$browser->setFilename($this->strDatabaseFilename);
		$browser->load();
		$browser->display();
	}
}*/

?>