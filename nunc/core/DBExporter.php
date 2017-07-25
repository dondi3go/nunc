<?php
require_once('DBCollection.php');
require_once('FileSystem.php');

class DBExporter
{
    public static function export(DBCollection $collection, $strDatabaseFilename)
    {
        $data = $collection->serialize();
        return FileSystem::saveFile($strDatabaseFilename, $data);
    }
}
?>