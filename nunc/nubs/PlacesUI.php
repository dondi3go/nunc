<?php
require_once(__DIR__.'/../more/DBViewUI.php');
require_once(__DIR__.'/../more/DBEditUI.php');
require_once(__DIR__.'/../more/DBDataUI.php');
require_once(__DIR__.'/../more/MapUI.php');
require_once(__DIR__.'/../main/Icon.php');
require_once(__DIR__.'/../main/Layout.php');
require_once(__DIR__.'/../nubs/Places.php');
require_once(__DIR__.'/../core/Converter.php');


class BriefPlaceUI implements IObjectUI
{
    public function getStrUI($place)
    {
        $name = Converter::HTMLEncode( $place->getPropertyValue(Place::Name) );
        $address = Converter::HTMLEncode( $place->getPropertyValue(Place::Address) );
        $str = "<b>".$name."</b>";
        $str.= "<p class='text-muted'>".$address."</p>";
        return $str;
    }
}

class FullPlaceUI implements IObjectUI
{
    public function getStrUI($place)
    {
        $name = Converter::HTMLEncode( $place->getPropertyValue(Place::Name) );
        $address = Converter::HTMLEncode( $place->getPropertyValue(Place::Address) );
        $note = Converter::HTMLEncode( $place->getPropertyValue(Place::Note) );
        $url = $place->getPropertyValue(Place::Url);

        $str = "";
        //if( strlen($url)>0 )
            $str.= "<a href='".$url."' target='_blank'><b>".$name."</b></a><br/>";
        //else
        //    $str.= "<b>".$name."</b><br/>";
        $str.= "<span class='text-muted'>".$address."</span><br/>";
        $str.= "<i>".$note."</i>";
        return $str;
    }
}

//
//
//
class PlacesViewUI implements IDisplayable
{
    // Map data
    private $strHeight = "25em"; // unit needed
    private $dLati = 45.5;
    private $dLongi = 4.3;
    private $iZoom = 5;
    private $bShowCoordinates = false;

    // Markers data
    private $collection;
    private $filename;      // url of xml file
    private $objectUI;      // object displayer

    public function PlacesViewUI()
    {
        $this->collection = new Places();
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function setObjectUI(IObjectUI $objectUI)
    {
        $this->objectUI = $objectUI;
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

    public function showCoordinates($bShowCoordinates)
    {
        $this->bShowCoordinates = $bShowCoordinates;
    }

    public function display(IPage $page)
    {
        // Create Map
        $ui = new MapUI();
        $ui->setHeight($this->strHeight);
        $ui->setZoom($this->iZoom);
        $ui->setCenter($this->dLati, $this->dLongi);
        $ui->showCoordinates($this->bShowCoordinates);

        // Add Markers
        if( $this->loadDB() )
        {
            $imax = $this->collection->getObjectCount();
            for( $i=0; $i<$imax; $i++)
            {
                $place = $this->collection->getObjectByIndex($i);
                $lati = $place->getPropertyValue(Place::Latitude);
                $longi = $place->getPropertyValue(Place::Longitude);
                $name = Converter::HTMLEncode( $place->getPropertyValue(Place::Name) );
                $address = Converter::HTMLEncode( $place->getPropertyValue(Place::Address) );
                $note = Converter::HTMLEncode( $place->getPropertyValue(Place::Note) );
                $url = $place->getPropertyValue(Place::Url);
                //if( strlen($url)>0 )
                    $strContent = "<b>".$name."</b><br/>";
                //else
                //    $strContent = "<a href='".$url."' target='_blank'><b>".$name."</b></a<br/>";
                $strContent.= "<i>".$address."</i><br/>".$note;

                //$strContent = $this->objectUI->getStrUI($place);

                $ui->addMarker($lati, $longi, $strContent);
            }
        }

        $ui->display($page);
    }

    private function loadDB()
    {
        if( isset($this->filename) )
        {
            if( Filesystem::fileExists($this->filename) )
            {
                return DBImporter::import($this->collection, $this->filename);
            }
        }
        return false;
    }
}

class PlacesUIFactory
{
    public static function createViewUI($filename)
    {
        $ui = new PlacesViewUI();
        $ui->setObjectUI(new BriefPlaceUI());
        $ui->setFilename($filename);
        $ui->setHeight("40em");
        $ui->setZoom(12);
        $ui->setCenter(48.86, 2.3455);
        $ui->showCoordinates(false);
        return $ui;
    }

    public static function createEditUI($filename)
    {
        $view = new PlacesViewUI();
        $view->setFilename($filename);
        $view->setHeight("20em");
        $view->setZoom(12);
        $view->setCenter(48.86, 2.3455);
        $view->showCoordinates(true);

        $edit = new DBEditUI();
        $edit->setCollection(new Places());
        $edit->setObjectUI(new BriefPlaceUI());
        $edit->setFilename($filename);

        $stack = new StackDisplay();
        $stack->add(new ContainerDisplay("", "<br/>\n", $view));
        $stack->add($edit);
        return $stack;
    }

    public static function createDataUI($filename)
    {
        $ui = new DBDataUI();
        $ui->setCollection(new Places());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createFullUI($filename)
    {
        $viewUI = PlacesUIFactory::createViewUI($filename);
        $editUI = PlacesUIFactory::createEditUI($filename);
        $dataUI = PlacesUIFactory::createDataUI($filename);

        $ui = new MidNavBar();
        $ui->addLeftItem($viewUI, "view", Icon::EYE);
        $ui->addLeftItem($editUI, "edit", Icon::PEN);
        $ui->addRightItem($dataUI, "data", Icon::CLOUD);
        return $ui;
    }
}

?>