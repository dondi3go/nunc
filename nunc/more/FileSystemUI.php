<?php

require_once(__DIR__.'/../core/FileSystem.php');
require_once(__DIR__.'/../main/IDisplayable.php');

//
//
//
class FileIdentifier
{
    public static function isPicture($filepath)
    {
        $ext = FileSystem::getFileExtension($filepath); // convert to lowercase
        if( ($ext=="jpg") or($ext=="png") ) {
            return true;
        }
        return false;
    }
}

//
//
//
class FileCounter
{
    public static function getPicturesCount($folderpath)
    {
        return 1;
    }

    public static function getPicturesWeight($folderpath)
    {
        return 32;
    }
}

//
//
//
interface FSAction
{
    const LIST_FILES     = 'fsaction_list_files';
    const ADD_FILE       = 'fsaction_add_file';
    const UPLOAD_FILE    = 'fsaction_file_upload';
    const VIEW_FILE      = 'fsaction_view_file';
    const DELETE_FILE    = 'fsaction_delete_file';
    const DELETE_CONFIRM = 'fsaction_delete_confirm';
    const RENAME_FILE    = 'fsaction_rename_file';
    const RENAME_CONFIRM = 'fsaction_rename_confirm';

    const PARAM_FILE_NAME     = 'fsaction_file_name';
    const PARAM_UPLOADED_FILE = 'fsaction_param_uploaded_file';
}

//
// Display files in a folder, sub folders not displayed
//
class FolderContentUI implements IDisplayable
{
    private $strAbsoluteFolderPath; // Root folder for files, ends with "/"

    public function FolderContentUI($strAbsoluteFolderPath)
    {
        $this->strAbsoluteFolderPath = $strAbsoluteFolderPath;
    }

    private function displayMessage($messageContent)
    {
        $str = "<div class='alert alert-success'>";
        $str.= $messageContent;
        $str.= "</div>\n";
        return $str;
    }

    private function getThumbnail($filepath)
    {
        $result = "";
        if(FileIdentifier::isPicture($filepath))
        {
            $url = FileSystem::convertPathToUrl($filepath);
            $style = "background-image: url(".$url.")";
            $result = "<div class='nunc_thumb' style='".$style."'></div>";
        }
        else
        {
            $result = "<div class='nunc_thumb' style='background-color:#F2F2F2'></div>";
        }
        return $result;
    }

    private function getFilePath($filename)
    {
        return $this->strAbsoluteFolderPath.$filename;
    }

    private function getFileSize($filepath)
    {
        return FileSystem::getFileSizeAsString($filepath);
    }

    public function display(IPage $page)
    {
        // Add inner CSS
        $css = ".nunc_thumb{width:5em;height:5em;overflow:hidden;background-size:cover;background-position:center center; background-repeat:no-repeat;}\n";
        $page->addInnerCSSContent($css);

        // Handle POST
        $str = "";
        if( isset($_POST[FSAction::ADD_FILE]) )
        {
            $str.= $this->displayMessage("Ready to add file");
            $str.= $this->displayAdd();
        }
        else if ( isset($_POST[FSAction::UPLOAD_FILE]) )
        {
            $newFileName = basename($_FILES[FSAction::PARAM_UPLOADED_FILE]['name']);
            $tmpFileLocation = $_FILES[FSAction::PARAM_UPLOADED_FILE]['tmp_name'];
            move_uploaded_file( $tmpFileLocation, $this->strAbsoluteFolderPath.$newFileName );
            $str.= $this->displayMessage("File <b>".$newFileName."</b> uploaded");
            $str.= $this->displayList();
        }
        else if ( isset($_POST[FSAction::VIEW_FILE]) )
        {
            $filename = $_POST[FSAction::VIEW_FILE];
            $str.= $this->displayMessage("View file <b>".$filename."</b>");
            $str.= $this->displayFile($filename);
        }
        else if( isset($_POST[FSAction::RENAME_FILE]) )
        {
            $filename = $_POST[FSAction::RENAME_FILE];
            $str.= $this->displayMessage("Rename <b>".$filename."</b>");
            $str.= $this->displayRename($filename);
        }
        else if( isset($_POST[FSAction::RENAME_CONFIRM]) )
        {
            $oldFilename = $_POST[FSAction::RENAME_CONFIRM];
            $newFilename = $_POST[FSAction::PARAM_FILE_NAME];
            $oldFilePath = $this->getFilePath($oldFilename);
            $newFilePath = $this->getFilePath($newFilename);
            FileSystem::rename($oldFilePath, $newFilePath);
            $str.= $this->displayMessage("<b>".$oldFilename."</b> renamed <b>".$newFilename."</b>");
            $str.= $this->displayFile($newFilename);
        }
        else if( isset($_POST[FSAction::DELETE_FILE]) )
        {
            $filename = $_POST[FSAction::DELETE_FILE];
            $str.= $this->displayMessage("Confirm deletion of <b>".$filename."</b>");
            $str.= $this->displayDelete($filename);
        }
        else if( isset($_POST[FSAction::DELETE_CONFIRM]) )
        {
            $filename = $_POST[FSAction::DELETE_CONFIRM];
            $filepath = $this->getFilePath($filename);
            FileSystem::delete($filepath);
            $str.= $this->displayMessage("File <b>".$filename."</b> deleted");
            $str.= $this->displayList();
        }
        else
        {
            $count = FileSystem::getFilesCount($this->strAbsoluteFolderPath);
            $size = FileSystem::getFilesSizeAsString($this->strAbsoluteFolderPath);
            $str.= $this->displayMessage($count." files, ".$size);
            $str.= $this->displayList();
        }

        $page->addBodyContent($str);
    }

    private function displayList()
    {
        $str = "<form method='post'>\n";

        // 'Add' button
        $str.= "<button class='btn btn-primary pull-right' name='".FSAction::ADD_FILE."' type='submit' value='novalue'>\n";
        $str.= "    ".Icon::ADD." add\n";
        $str.= "</button>\n";
        $str.= "<br/><br/>\n";

        // Table start
        $str.= "<table class='table table-condensed'>\n";

        // Display Files
        $filenames = FileSystem::getFiles($this->strAbsoluteFolderPath);
        foreach ($filenames as $filename)
        {
            $filepath = $this->getFilePath($filename);
            $filesize = $this->getFileSize($filepath);

            $col1 = $this->getThumbnail($filepath);

            $col2 = "<b>".$filename."</b><br/>";
            $col2.= "<p class='text-muted small'>".$filesize."</p>";

            $col3 = "<button class='btn btn-link' name='".FSAction::VIEW_FILE."' type='submit' value='".$filename."'>\n";
            $col3.= Icon::RIGHT."</button>";

            $str.= "<tr>\n";
            $str.= "<td>".$col1."</td>\n";
            $str.= "<td>".$col2."</td>\n";
            $str.= "<td class='text-right'>".$col3."</td>\n";
            $str.= "</tr>\n";
        }

        // Table stop
        $str.= "</table>\n";

        $str.= "</form>\n";

        return $str;
    }

    private function displayAdd()
    {
        $str = "<form method='post' enctype='multipart/form-data'>\n";

        // 'Back' button
        $str.= "<button class='btn btn-primary pull-left' name='".FSAction::LIST_FILES."' type='submit' value='novalue'>\n";
        $str.= "    ".Icon::LEFT." back\n";
        $str.= "</button>\n";
        $str.= "<br/><br/>\n";

        // Table start
        $str.= "<table class='table table-condensed'><tr><td>\n";

        // Upload
        $str.= "<div class='well text-center'>\n";
        
        $str.= "<label class='btn btn-primary'>\n";
        $str.= Icon::FOLDER."&nbsp;&nbsp;browse <input type='file' style='display:none' name='".FSAction::PARAM_UPLOADED_FILE."'/>\n";
        $str.= "</label>\n";
        $str.= "<button class='btn btn-primary' name='".FSAction::UPLOAD_FILE."' type='submit' value='novalue'>";
        $str.= "    ".Icon::CLOUD_UP." upload\n";
        $str.= "</button>\n";
        
        /*$str.= "<input class='form-control' type='file' name='".FSAction::PARAM_UPLOADED_FILE."'/>\n";
        $str.= "<input class='btn btn-default' type='submit' name='".FSAction::UPLOAD_FILE."' value='Upload'/>\n";*/
        
        $str.= "</div>";

        // Table stop
        $str.= "</td></tr></table>\n";

        $str.= "</form>\n";

        return $str;
    }

    private function displayFile($filename)
    {
        $str = "<form method='post'>\n";

        // 'Back' button
        $str.= "<button class='btn btn-primary pull-left' name='".FSAction::LIST_FILES."' type='submit'>\n";
        $str.= "    ".Icon::LEFT." back\n";
        $str.= "</button>\n";

        // 'Rename' / 'Delete' buttons
        $str.= "<div class='btn-toolbar pull-right' role='toolbar' aria-label='...'>\n";
        $str.= "<button class='btn btn-primary' name='".FSAction::RENAME_FILE."' type='submit' value='".$filename."'>\n";
        $str.= "    ".Icon::PEN." rename\n";
        $str.= "</button>\n";
        $str.= "<button class='btn btn-primary' name='".FSAction::DELETE_FILE."' type='submit' value='".$filename."'>\n";
        $str.= "    ".Icon::CROSS." delete\n";
        $str.= "</button>\n";
        $str.= "</div>\n";

        $str.= "</form>";

        $str.= "<br/><br/>\n";

        $str.= "<table class='table table-condensed'><tr><td>\n";
        $str.=$this->displayFileDetail($filename);
        $str.= "</table>\n";

        return $str;
    }

    private function displayRename($filename)
    {
        // 'Back' button
        $str = "<form method='post'>\n";
        $str.= "<button class='btn btn-primary pull-left' name='".FSAction::VIEW_FILE."' type='submit' value='".$filename."'>\n";
        $str.= "    ".Icon::LEFT." back\n";
        $str.= "</button>\n";
        $str.= "</form>\n";

        $str.= "<br/><br/>\n";

        // Confirm rename

        $str.= "<div class='well'>";
        $str.= "<form method='post' class='form-inline text-center'>\n";
        $str.= "<div class='form-group'>\n";
        $str.= "<input class='form-control' name='".FSAction::PARAM_FILE_NAME."' placeholder='".$filename."'>\n";
        $str.= "<button class='btn btn-primary' name='".FSAction::RENAME_CONFIRM."' type='submit' value='".$filename."'>\n";
        $str.= "    ".Icon::PEN." Rename\n";
        $str.= "</button>\n";
        $str.= "</div>\n";
        $str.= "</form>\n";
        $str.= "</div>";

        $str.=$this->displayFileDetail($filename);

        return $str;
    }

    private function displayDelete($filename)
    {
        // 'Back' button
        $str = "<form method='post'>\n";
        $str.= "<button class='btn btn-primary pull-left' name='".FSAction::VIEW_FILE."' type='submit' value='".$filename."'>\n";
        $str.= "    ".Icon::LEFT." back\n";
        $str.= "</button>\n";
        $str.= "</form>\n";

        $str.= "<br/><br/>\n";

        // Confirm Delete

        $str.= "<div class='well'>";
        $str.= "<form method='post' class='form-inline text-center'>\n";
        $str.= "<div class='form-group'>\n";
        $str.= "<button class='btn btn-primary center-block' name='".FSAction::DELETE_CONFIRM."' type='submit' value='".$filename."'>\n";
        $str.= "    ".Icon::CROSS." Delete\n";
        $str.= "</button>\n";
        $str.= "</div>\n";
        $str.= "</form>\n";
        $str.= "</div>";

        $str.=$this->displayFileDetail($filename);

        return $str;
    }

    private function displayFileDetail($filename)
    {
        $filepath = $this->getFilePath($filename);
        $filesize = $this->getFileSize($filepath);
        $col1 = $this->getThumbnail($filepath);
        $col2 = "<b>".$filename."</b><br/>";
        $col2.= "<p class='text-muted small'>".$filesize."</p>";

        //
        $str.= "<div class='row'>\n";
        $str.= "<div class='col-xs-3'>".$col1."</div>\n";
        $str.= "<div class='col-xs-9'>".$col2."</div>\n";
        $str.= "</div>\n";

        return $str;
    }
}

//
//
//
interface TextFileContentAction
{
    const CREATE      = 'textfilecontent_create';
    const EDIT        = 'textfilecontent_edit';
    const CANCEL      = 'textfilecontent_cancel';
    const SAVE        = 'textfilecontent_save';
    const DELETE      = 'textfilecontent_delete';
}

//
//
//
class TextFileContentUI implements IDisplayable
{
    private $strAbsoluteFilePath;

    public function TextFileContentUI($strAbsoluteFilePath)
    {
        $this->strAbsoluteFilePath = $strAbsoluteFilePath;
    }

    private function displayErrorMessage($messageContent)
    {
        $str = "<div class='alert alert-danger text-center'>";
        $str.= $messageContent;
        $str.= "</div>\n";
        return $str;
    }

    private function checkPost($var)
    {
        return isset($_POST[$var]);
    }

    public function display(IPage $page)
    {
        if( $this->checkPost(TextFileContentAction::CREATE) )
        {
            FileSystem::saveFile($this->strAbsoluteFilePath, "");
            $this->displayView($page);
        }
        else if( $this->checkPost(TextFileContentAction::DELETE) )
        {
            FileSystem::delete($this->strAbsoluteFilePath);
            $this->displayView($page);
        }
        else if( $this->checkPost(TextFileContentAction::EDIT) )
        {
            $this->displayEdit($page);
        }
        else if( $this->checkPost(TextFileContentAction::SAVE) )
        {
            $content = $_POST["filecontent"];
            FileSystem::saveFile($this->strAbsoluteFilePath, $content);
            $this->displayView($page);
        }
        else
        {
            $this->displayView($page);
        }
    }

    public function displayEdit(IPage $page)
    {
        $str = "";

        if(!FileSystem::fileExists($this->strAbsoluteFilePath))
        {
            $str.= "<table class='table'><tr><td>";
            $str.= $this->displayErrorMessage("file not found");
            $str.= "</td></tr></table>";
        }
        else
        {
            $str = "<form method='post'>\n";
            $str.= "<div class='row'><div class='col-md-12'>\n";

            // 'back' Button
            $str.= "<button class='btn btn-default pull-left' name='".TextFileContentAction::CANCEL."' type='submit'>\n";
            $str.= "    ".ICON::LEFT." back\n";
            $str.= "</button>\n";

            // 'save' Button
            $str.= "<button class='btn btn-default pull-right' name='".TextFileContentAction::SAVE."' type='submit'>\n";
            $str.= "    ".ICON::SAVE." save\n";
            $str.= "</button>\n";

            $str.= "<br/><br/>\n";

            $str.= "</div></div>\n";

            $str.= "<table class='table'><tr><td>";
            // File Content
            $strText = FileSystem::loadFile($this->strAbsoluteFilePath);
            $str.= "<textarea class='form-control' rows='10' type='text' name='filecontent'>".$strText."</textarea><br/>\n";
            $str.= "</td></tr></table>";
            $str.= "</form>\n";
        }
        $page->addBodyContent($str);

        //$page->addBodyContent($this->displayErrorMessage("edit"));
    }

    public function displayView(IPage $page)
    {
        $str = "";

        if(!FileSystem::fileExists($this->strAbsoluteFilePath))
        {
            $str = "<form method='post'>\n";
            $str.= "<div class='row'><div class='col-md-12'>\n";

            // 'create' Button
            $str.= "<button class='btn btn-default pull-right' name='".TextFileContentAction::CREATE."' type='submit'>\n";
            $str.= "    ".ICON::ADD." create\n";
            $str.= "</button>\n";
            $str.= "<br/><br/>\n";

            $str.= "</div></div>\n";

            $str.= "<table class='table'><tr><td>";
            // Message
            $str.= $this->displayErrorMessage("file not found");
            $str.= "</td></tr></table>";
            $str.= "</form>\n";
        }
        else
        {
            $str = "<form method='post'>\n";
            $str.= "<div class='row'><div class='col-md-12'>\n";

            // 'edit' Button
            $str.= "<button class='btn btn-default pull-right' name='".TextFileContentAction::EDIT."' type='submit'>\n";
            $str.= "    ".ICON::PEN." edit\n";
            $str.= "</button>\n";
            $str.= "<br/><br/>\n";

            $str.= "</div></div>\n";

            $str.= "<table class='table'><tr><td>";

            // File Content
            $strText = FileSystem::loadFile($this->strAbsoluteFilePath);
            $str.= "<textarea class='form-control' rows='10' type='text' name='filecontent' readonly>".$strText."</textarea><br/>\n";
            $str.= "</td></tr></table>";

            // 'delete' Button
            $str.= "<button class='btn btn-default pull-right' name='".TextFileContentAction::DELETE."' type='submit'>\n";
            $str.= "    ".ICON::CROSS." delete\n";
            $str.= "</button>\n";

            $str.= "</form>\n";
        }
        $page->addBodyContent($str);
    }
}


?>