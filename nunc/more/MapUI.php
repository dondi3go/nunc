<?php
require_once(__DIR__.'/../Config.php');
require_once(__DIR__.'/../main/IDisplayable.php');
require_once(__DIR__.'/../main/Icon.php');

//
//
//
class Marker
{
    public $dLati;
    public $dLongi;
    public $strContent;

    public function Marker($dLati, $dLongi, $strContent)
    {
        $this->dLati = $dLati;
        $this->dLongi = $dLongi;
        $this->strContent = $strContent;
    }
}

//
//
//
class MapUI implements IDisplayable
{
    // Geosearch
    private $strSearchLabel = "address";
    private $strAddressUnknown = "not found ...";

    // Map itself
    private $strWidth = "100%";
    private $strHeight = "500px";
    private $dLati = 45.5;
    private $dLongi = 4.3;
    private $iZoom = 5;
    private $bShowCoordinates = false;

    // Markers
    private $markers = array(); // list of markers

    private function addLibraries($page)
    {
        $page->addCSS(Config::leaflet_css);
        $page->addJS(Config::leaflet_js);
        $page->addCSS(Config::leaflet_geosearch_css);
        $page->addJS(Config::leaflet_geosearch_js);
        $page->addJS(Config::leaflet_geosearch_osm_js);
    }

    public function display(IPage $page)
    {
        $this->addLibraries($page);

        // The div for the map
        $str  = "<div id='map' style='height: ".$this->strHeight."; width: ".$this->strWidth."; overflow: hidden;'></div>\n";

        // The script
        $str .= "<script type='text/javascript'>\n";

        $str .= "var layerOSM = L.tileLayer('http://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {\n";
        $str .= "  maxZoom: 19,\n";
        $str .= "});\n";

        $str .= "var map = L.map('map', {\n";
        $str .= "  zoom: ".$this->iZoom.",\n";
        $str .= "  minZoom: 2,\n";
        $str .= "  center: new L.LatLng(".$this->dLati.", ".$this->dLongi."),\n";
        $str .= "  layers: [layerOSM],\n";
        $str .= "  attributionControl: false\n";
        $str .= "});\n";

        // Scale control
        $str .= "new L.control.scale({\n";
        $str .= "  imperial: false\n";
        $str .= "}).addTo(map);\n";

        // Search control
        $str .= "new L.Control.GeoSearch({\n";
        $str .= "  provider: new L.GeoSearch.Provider.OpenStreetMap(),\n";
        $str .= "  searchLabel:'".$this->strSearchLabel."',\n";
        $str .= "  notFoundMessage:'".$this->strAddressUnknown."'\n";
        $str .= "}).addTo(map);\n";

        // Add markers
        foreach ($this->markers as $marker)
        {
            $strMarker = "L.marker([".$marker->dLati.", ".$marker->dLongi."])";
            $strPopup = "L.popup().setContent('".$marker->strContent."')";
            $str .= $strMarker.".addTo(map).bindPopup(".$strPopup.", {closeButton:false}).openPopup();\n";
        }

        // On Resize (optionnal : full screen only)
		if( $this->strHeight == "100%")
		{
			$str .= "$(window).on('resize', function() {\n";
			$str .= "    $('#map').height($(window).height()-150).width($(window).width());\n";
			$str .= "    map.invalidateSize();\n";
			$str .= "}).trigger('resize');\n";
		}

        if($this->bShowCoordinates)
        {
            $str.= "var popup = L.popup();\n";
            $str.= "function onMapClick(e) {\n";
            $str.= "    popup\n";
            $str.= "        .setLatLng(e.latlng)\n";
            $str.= "        .setContent(e.latlng.toString())\n";
            $str.= "        .openOn(map);\n";
            $str.= "}\n";
            $str.= "map.on('click', onMapClick);\n";
        }

        $str .= "</script>\n";

        $page->addBodyContent($str);
    }

    public function setSearchLabel($str)
    {
        $this->strSearchLabel = $str;
    }

    public function setAddressUnknown($str)
    {
        $this->strAddressUnknown = $str;
    }

    public function setHeight($strHeight)
    {
        $this->strHeight = $strHeight;
    }

    public function setCenter($dLati, $dLongi)
    {
        $this->dLati = $dLati;
        $this->dLongi = $dLongi;
    }

    public function setZoom($iZoom)
    {
        $this->iZoom = $iZoom;
    }

    public function addMarker($dLati, $dLongi, $strContent)
    {
        $this->markers[] = new Marker($dLati, $dLongi, $strContent);
    }

    public function showCoordinates($bShowCoordinates)
    {
        $this->bShowCoordinates = $bShowCoordinates;
    }

    // Show coordinates on click
    /*function showCoordinates()
    {
        echo "map.on('click', function(e) {\n";
        echo "  alert('Lat, Lon : ' + e.latlng.lat + ', ' + e.latlng.lng);\n";
        echo "});\n";
    }

    // Add a new layer
    function addLayer($strLayerName, $strIcon, $strColor)
    {   
        echo "var layerMarker = L.AwesomeMarkers.icon({\n";
        echo "    icon:'".$strIcon."',\n";
        echo "    markerColor:'".$strColor."'\n";
        echo "});\n";
    }*/
}
?>