<?php

require_once(__DIR__.'/../core/FileSystem.php');
require_once(__DIR__.'/../core/DBImporter.php');
require_once(__DIR__.'/../core/Converter.php');
require_once(__DIR__.'/../main/IDisplayable.php');
require_once(__DIR__.'/../main/Icon.php');
require_once(__DIR__.'/../more/DBCommonUI.php');

//
//
//
interface DBEditAction
{
    const LIST_OBJECTS    = 'dbedit_list_objects';
    const VIEW_OBJECT     = 'dbedit_view_object';
    const EDIT_OBJECT     = 'dbedit_edit_object';
    const SAVE_OBJECT     = 'dbedit_save_object';
    const ADD_OBJECT      = 'dbedit_add_object';
    const REMOVE_OBJECT   = 'dbedit_remove_object';
    const PROPERTY_PREFIX = 'dbedit_property_';
}

//
//
//
class DBEditUI implements IDisplayable
{
    private $collection;
    private $filename;       // url of xml file
    private $listObjectUI;   // object displayer (list)


    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function setObjectUI(IObjectUI $objectUI)
    {
        $this->listObjectUI = $objectUI;
    }

    private function addLibraries(IPage $page)
    {
        $page->addCSS(Config::bootstrap_css);

        $css = ".btn-no-style {background-color:transparent;border:none;outline:none;text-align:left;}\n";
        $page->addInnerCSSContent($css);
    }

    public function display(IPage $page)
    {
        $this->addLibraries($page);

        $str = "";
        if( $this->loadDB() )
        {
            if( isset($_POST[DBEditAction::VIEW_OBJECT]) )
            {
                $uid = $_POST[DBEditAction::VIEW_OBJECT];
                $str.= $this->displayMessage("View object ".$uid);
                $str.= $this->displayViewObject($uid);
            }
            else if( isset($_POST[DBEditAction::EDIT_OBJECT]) )
            {
                $uid = $_POST[DBEditAction::EDIT_OBJECT];
                $str.= $this->displayMessage("Edit object ".$uid);
                $str.= $this->displayEditObject($uid);
            }
            else if( isset($_POST[DBEditAction::SAVE_OBJECT]) )
            {
                $uid = $_POST[DBEditAction::SAVE_OBJECT];
                $object = $this->collection->getObjectByUid($uid);
                foreach ($_POST as $key => $value)
                {
                    $pos = strpos( $key, DBEditAction::PROPERTY_PREFIX );
                    if( !($pos === false) )
                    {
                        $strTag = substr($key, strlen(DBEditAction::PROPERTY_PREFIX));
                        $value = Converter::HTMLDecode($value);
                        $object->setPropertyValue($strTag, $value);
                    }
                }
                $this->saveDB();
                $str.= $this->displayMessage("Object ".$uid." saved");
                $str.= $this->displayViewObject($uid);
            }
            else if( isset($_POST[DBEditAction::ADD_OBJECT]) )
            {
                $object = $this->collection->addObject();
                $uid = $object->getUid();
                $this->saveDB();
                $str.= $this->displayMessage("Object ".$uid." added");
                $str.= $this->displayEditObject($uid);
            }
            else if( isset($_POST[DBEditAction::REMOVE_OBJECT]) )
            {
                $uid = $_POST[DBEditAction::REMOVE_OBJECT];
                $object = $this->collection->getObjectByUid($uid);
                $this->collection->removeObject( $object );
                $this->saveDB();
                $str.= $this->displayMessage("Object ".$uid." removed");
                $str.= $this->displayList();
            }
            else
            {
                $count = $this->collection->getObjectCount();
                $str .= $this->displayMessage($count." objects loaded");
                $str .= $this->displayList();
            }
        }
        else
        {
            $str.= $this->displayMessage("no data");
        }
        $page->addBodyContent($str);
    }

    private function displayMessage($messageContent)
    {
        $str = "<div class='alert alert-success'>";
        $str.= $messageContent;
        $str.= "</div>\n";
        return $str;
    }

    private function displayList()
    {
        $str = "<form method='post'>\n";
        
        // 'Add' button
        $str.= "<button class='btn btn-primary pull-right' name='".DBEditAction::ADD_OBJECT."' type='submit' value='novalue'>\n";
        $str.= "    ".Icon::ADD." add\n";
        $str.= "</button>\n";
        $str.= "<br/><br/>\n";
        
        // List items
        $str.= "<table class='table table-condensed'>\n";
        $col = new DBReverseCollection($this->collection); // newer first
        $imax = $col->getObjectCount();
        for( $i=0; $i<$imax; $i++)
        {
            $object = $col->getObjectByIndex($i);
            $str.= $this->displayListObject($object);
        }
        $str.= "</table>\n";

        $str.= "</form>\n";
        return $str;
    }

    private function displayListObject($object)
    {
        $uid = $object->getUid();
        
        $ui1A = "<span class='text-left'>".$this->listObjectUI->getStrUI($object)."</span>";
        $ui1B = "<button class='btn-no-style' name='".DBEditAction::VIEW_OBJECT."' type='submit' value='".$uid."'>".$ui1A."</button>";
        //$ui1B = $ui1A;

        $ui2A = Icon::RIGHT;
        $ui2B = "<button class='btn btn-link' name='".DBEditAction::VIEW_OBJECT."' type='submit' value='".$uid."'>".$ui2A."</button>";

        $str = "<tr><td>".$ui1B."</td><td class='text-right'>".$ui2B."</td></tr>\n";
        return $str;
    }

    private function displayViewObject($uid)
    {
        $str = "<form method='post'>\n";

        // 'Back' button
        $str.= "<button class='btn btn-primary pull-left' name='".DBEditAction::LIST_OBJECTS."' type='submit' value='novalue'>\n";
        $str.= "    ".Icon::LEFT." back\n";
        $str.= "</button>\n";

        // 'Edit' Button
        $str.= "<button class='btn btn-primary pull-right' name='".DBEditAction::EDIT_OBJECT."' type='submit' value='".$uid."'>\n";
        $str.= "    ".Icon::PEN." edit\n";
        $str.= "</button>\n";

        $str.= "<br/><br/>\n";

        // Object Properties
        $object = $this->collection->getObjectByUid($uid);
        $viewObjectUI = new PropertiesViewObjectUI();
        $str.= $viewObjectUI->getStrUI($object);

        // 'Remove' button
        $str.= "<button class='btn btn-primary pull-right' name='".DBEditAction::REMOVE_OBJECT."' type='submit' value='".$uid."'>\n";
        $str.= "    ".Icon::CROSS." remove\n";
        $str.= "</button>\n";

        $str.= "</form>\n";

        return $str;
    }

    private function displayEditObject($uid)
    {
        $str = "<form method='post'>\n";

        // 'Back' button
        $str.= "<button class='btn btn-primary pull-left' name='".DBEditAction::VIEW_OBJECT."' type='submit' value='".$uid."'>\n";
        $str.= "    ".Icon::LEFT." back\n";
        $str.= "</button>\n";

        // 'Edit' Button
        $str.= "<button class='btn btn-primary pull-right' name='".DBEditAction::SAVE_OBJECT."' type='submit' value='".$uid."'>\n";
        $str.= "    ".Icon::SAVE." save\n";
        $str.= "</button>\n";

        $str.= "<br/><br/>\n";

        // Object Properties
        $object = $this->collection->getObjectByUid($uid);
        $editObjectUI = new PropertiesEditObjectUI();
        $editObjectUI->setPropertyPrefix(DBEditAction::PROPERTY_PREFIX);
        $str.= $editObjectUI->getStrUI($object);

        $str.= "</form>\n";

        return $str;
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

    private function saveDB()
    {
        if( isset($this->filename) )
        {
            if( Filesystem::fileExists($this->filename) )
            {
                return DBExporter::export($this->collection, $this->filename);
            }
        }
        return false;
    }
}

?>