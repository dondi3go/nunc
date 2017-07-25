<?php
require_once('DBCollection.php');
require_once('FileSystem.php');

class DBImporter
{
    public static function import(DBCollection $collection, $strDatabaseFilename)
    {
        if( false == FileSystem::fileExists($strDatabaseFilename) )
        {
            return false;
        }
        $data = FileSystem::loadFile($strDatabaseFilename);
        if( false == $data )
        {
            return false;
        }
        return $collection->deserialize($data);
    }
}
?>