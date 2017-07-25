<?php
//
//
//
class FileSystem
{
    //
    // Return true or false
    //
    static public function fileExists($strFilename)
    {
        return file_exists($strFilename);
    }

    //
    //  Return file content
    //
    static public function loadFile($strFilename)
    {
        // Check that file exists
        $handle = fopen($strFilename,"r");
        if( $handle == NULL )
        {
            // File not found
            return false;
        }

        if( !FileSystem::waitForReadLock($handle, 0.5, 0.02) )
        {
            // Cannot get lock
            return false;
        }

        $strContent = file_get_contents($strFilename);
        fclose($handle);

        return $strContent;
    }

    //
    // Return true or false
    //
    static public function saveFile($strFilename, $strContent)
    {
        $handle = fopen($strFilename,"w+");
        if( $handle == NULL )
        {
            // Cannot open File
            return false;
        }

        if( !FileSystem::waitForWriteLock($handle, 0.5, 0.02) )
        {
            // Cannot get lock
            return false;
        }

        file_put_contents($strFilename, $strContent);
        fclose($handle);

        return true;
    }

    //
    // $totalWait, $retryWait : seconds
    //
    static public function waitForReadLock($handle, $totalWait, $retryWait)
    {
        $currentWait = 0;
        $isLocked = flock($handle, LOCK_SH); 
        while( !$isLocked && ($currentWait <= $totalWait) )
        {
            $currentWait += $retryWait; 
            usleep($retryWait*1000000); // seconds to micro-seconds
            $isLocked = flock($handle, LOCK_SH);
        }
        return $isLocked;
    }

    //
    // $totalWait, $retryWait : seconds
    //
    static public function waitForWriteLock($handle, $totalWait, $retryWait)
    {
        $currentWait = 0;
        $isLocked = flock($handle, LOCK_EX); 
        while( !$isLocked && ($currentWait <= $totalWait) )
        {
            $currentWait += $retryWait; 
            usleep($retryWait*1000000); // seconds to micro-seconds
            $isLocked = flock($handle, LOCK_EX);
        }
        return $isLocked;
    }

    //
    // Returns the extension of a file
    //
    public static function getFileExtension($strFilename)
    {
        $extension = explode('.', $strFilename);
        $extension = array_reverse($extension);
        $extension = $extension[0];
        return $extension;

        // Only works if the file is on disk, not for a non yet existing file
        //return pathinfo($strFilename, PATHINFO_EXTENSION);
    }

    //
    // An encapsulation of basename() function : filename + extension, no path
    //
    public static function getFileBasename($strFilename)
    {
        return basename($strFilename); // operates on string
    }

    //
    // Creates a new folder
    //
    public static function createFolder( $dir, $newSubDir )
    {
        // Check that new name is valid
        // Check that the subDir does not already exist
        
        // Create the new sub directory
        
        mkdir( $dir."/".$newSubDir );

        return true;
    }

    //
    // Creates a new file
    //
    public static function createFile( $dir, $strFilename )
    {
        // Check that new name is valid
        // Check that the file does not already exist
        
        // Create the new file
        $file = fopen($dir."/".$strFilename, "w");
        fclose($file);

        return true;
    }

    //
    // Returns every folders (basename only) in a folder
    //
    public static function getFolders($dir)
    {
        if( is_dir($dir) )
        {
            $result = array();
            
            $handle = opendir($dir);
            while (false !== ($item = readdir($handle))) 
            {
                if( ($item!=".")&&($item!="..") )
                {
                    if( is_dir( "$dir/$item" ) )
                    {
                        $result[] = $item;
                    }
                }
            }
            closedir($handle);
            
            // Todo : sort
            
            return $result;
        }
    }

    //
    //
    //
    public static function getFoldersCount($dir)
    {
        $basenames = FileSystem::getFolders($dir);
        return sizeof($basenames);
    }

    //
    // Returns every file (basename only) in a folder
    //
    public static function getFiles($dir)
    {
        if( is_dir($dir) )
        {
            $result = array();
            
            $handle = opendir($dir);
            while (false !== ($item = readdir($handle))) 
            {
                if( ($item!=".")&&($item!="..") )
                {
                    if( !is_dir( "$dir/$item" ) )
                    {
                        $result[] = $item;
                    }
                }
            }
            closedir($handle);
            
            // Todo : sort ?
            
            return $result;
        }
    }

    //
    // Does not count files in subfolders
    //
    public static function getFilesCount($folderPath)
    {
        $basenames = FileSystem::getFiles($folderPath);
        return sizeof($basenames);
    }

    //
    // Does not count files in subfolders
    //
    public static function getFilesSizeAsString($folderPath)
    {
        $totalSize = 0;
        $filenames = FileSystem::getFiles($folderPath);
        foreach ($filenames as $filename)
        {
            $filepath = $folderPath.$filename;
            $totalSize += filesize($filepath);
        }
        return FileSystem::convertSizeToString($totalSize);
    }

    //
    // Rename a file or a folder
    //
    public static function rename( $item, $itemNewName )
    {
        // check that new name is valid
        // Check that new name does not already exist
        
        // Change the name
        rename( $item, $itemNewName );

        return true;
    }
    
    //
    // Delete a file or a folder
    //
    public static function delete( $item )
    {
        if( is_dir( $item ) )
        {
            rmdir( $item );
        }
        else
        {
            unlink( $item );
        }
        return true;
    }

    //
    //  Remove every files and subfolders inside a folder
    //
    public static function clearFolder( $strFoldername )
    {
        if( is_dir( $strFoldername ) )
        {
            $basenames = FileSystem::getFolders( $strFoldername );
            foreach($basenames as $basename)
            {
                $strPath = $strFoldername."/".$basename;
                FileSystem::delete( $strPath );
            }

            $basenames = FileSystem::getFiles( $strFoldername );
            foreach($basenames as $basename)
            {
                $strPath = $strFoldername."/".$basename;
                FileSystem::delete( $strPath );
            }
        }
    }

    //
    // Get creation date as string
    //
    public static function getCreationDateAsString( $strFilename )
    {
        return date("Y-m-d H:i:s", filemtime( $strFilename ));
    }

    //
    // Get last modification date as string
    //
    public static function getModificationDateAsString( $strFilename )
    {
        return date("Y-m-d H:i:s", filectime( $strFilename )); // change time
    }

    //
    //
    //
    public static function getFileSizeAsString($strFilename)
    {
        $size = filesize($strFilename);
        return FileSystem::convertSizeToString($size);
    }

    private static function convertSizeToString($size)
    {
        if( $size > 1024*1024 )
        {
            $result = round( $size/(1024*1024), 1 )." Mb";
        }
        else if( $size > 1024 )
        {
            $result = round( $size/1024, 1 )." kb";
        }
        else
        {
            $result = $size." b";
        }
        return $result;
    }

    //
    //
    //
    public static function createCryptedPath( $strFilename, $strDstFolder )
    {
        // crypted path never change :
        // - convenient because less operations are done at each access
        // - drawback for security (persistance)
        $strPath = $strDstFolder."/".sha1($strFilename).".".FileSystem::getFileExtension($strFilename);
        return $strPath;
    }

    //
    // Return path to symbolic link pointing to $strFilename located in $strDstFolder
    // filename is crypted, not content
    //
    public static function getCryptedLink($strFilename, $strDstFolder)
    {
        $strPath = FileSystem::createCryptedPath( $strFilename, $strDstFolder );

        if( FileSystem::fileExists($strPath) == false )
        {
            symlink( $strFilename, $strPath );
        }

        return $strPath;
    }

    //
    // Return path to a hardcopy of $strFilename located in $strDstFolder
    // filename is crypted, not content
    //
    public static function getCryptedCopy($strFilename, $strDstFolder)
    {
        $strPath = FileSystem::createCryptedPath( $strFilename, $strDstFolder );

        if( FileSystem::fileExists($strPath) == false )
        {
            copy( $strFilename, $strPath );
        }

        return $strPath;
    }

    //
    // Path should be under DOCUMENT_ROOT
    //
    public static function convertPathToUrl($strPath)
    {
        $url = "";

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
        {
            $url = str_replace($_SERVER['DOCUMENT_ROOT'], 'https://'.$_SERVER['HTTP_HOST'], $strPath);
        }
        else
        {
            $url = str_replace($_SERVER['DOCUMENT_ROOT'], 'http://'.$_SERVER['HTTP_HOST'], $strPath);
        }
        return $url;
    }
}

/*
//
function getDirsAndDocsCountStr($dir)
{
    if( is_dir($dir) )
    {
        $dirCnt = 0;
        $docCnt = 0;
        
        $handle = opendir($dir);
        while (false !== ($item = readdir($handle))) 
        {
            if( ($item!=".")&&($item!="..") )
            {
                if( is_dir( "$dir/$item" ) )
                {
                    $dirCnt ++;
                }
                else
                {
                    $docCnt ++;
                }
            }
        }
        closedir($handle);
        
        $dirStr = "";
        if( $dirCnt == 1 )
        {
            $dirStr = "1 dossier";
        }
        if( $dirCnt > 1 )
        {
            $dirStr = $dirCnt." dossiers";
        }
        
        $docStr = "";
        if( $docCnt == 1 )
        {
            $docStr = "1 document";
        }
        if( $docCnt > 1 )
        {
            $docStr = $docCnt." documents";
        }
        
        $result = "";
        if( $dirCnt > 0 )
        {
            $result = $result.$dirStr;
            if( $docCnt > 0 )
            {
                $result = $result." - ";
            }
        }
        if( $docCnt > 0 )
        {
            $result = $result.$docStr;
        }
        
        if( ( $dirCnt == 0 )&&( $docCnt == 0) )
        {
            $result = "vide";
        }
        
        return $result;
    }
    return "";
}*/

?>