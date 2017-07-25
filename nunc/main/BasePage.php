<?php
require_once('IPage.php');

///
/// An HTML page
///
class BasePage implements IPage
{
    private $strTitle;
    private $strKeywords;
    private $strDescription;
    private $strLanguage;
    private $strIcon;
    private $strHeight;

    private $bEnforceHTTPS = false;

    private $strCSSFiles = array();
    private $strJSFiles = array();

    private $strInnerCSSContent;
    private $strBodyContent;

    public function setTitle($strTitle)
    {
        $this->strTitle = $strTitle;
    }

    public function setKeywords($strKeywords)
    {
        $this->strKeywords = $strKeywords; //utf8_encode not needed, the page is utf8
    }
    
    public function setDescription($strDescription)
    {
        $this->strDescription = $strDescription; //utf8_encode not needed, the page is utf8
    }

    public function setLanguage($strLanguage)
    {
        $this->strLanguage = $strLanguage;
    }

    public function setIcon($strIcon)
    {
        $this->strIcon = $strIcon;
    }

    public function enforceHTTPS()
    {
        $this->bEnforceHTTPS = true;
    }

    public function addJS($strFileUrl)
    {
        if (!in_array($strFileUrl, $this->strJSFiles))
        {
            $this->strJSFiles[] = $strFileUrl;
        }
    }

    public function addCSS($strFileUrl)
    {
        if (!in_array($strFileUrl, $this->strCSSFiles))
        {
            $this->strCSSFiles[] = $strFileUrl;
        }
    }

    // --- Add inner CSS content

    public function addInnerCSSContent($str)
    {
        $this->strInnerCSSContent .= $str;
    }

    // --- Add content

    public function addBodyContent($str)
    {
        $this->strBodyContent .= $str;
    }

    // --- What is it for ? setHeight(100%) for maps for instance

    public function setHeight($strHeight)
    {
        $this->strHeight = $strHeight;
    }

    // ----------------------------------

    public function flush()
    {
        // --- HTTPS ---
        if( $this->bEnforceHTTPS )
        {
            if($_SERVER["HTTPS"] != "on")
            {
                header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
                exit();
            }
        }

        echo "<!DOCTYPE html>\n";

        // --- DEBUG mode --- MAKE IT AN OPTION
        //ini_set('display_errors', 'On');

        // --- Language ---
        if( !empty( $this->strLanguage ) ){
            echo "<html lang='".$this->strLanguage."'>\n";
        }
        else{
            echo "<html>\n";
        }
        
        echo "<head>\n";

         // --- Title ---
        if( !empty( $this->strTitle ) ){
            echo "<title>".$this->strTitle."</title>\n";
        }
        
        echo "<meta http-equiv='content-type' content='text/html;charset=utf-8'/>\n";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'/>\n";

        // --- CSS files ---
        foreach ($this->strCSSFiles as $strCSSFile)
        {
            echo "<link href='".$strCSSFile."' rel='stylesheet' media='screen'/>\n";
            echo "<link href='".$strCSSFile."' rel='stylesheet' media='print'/>\n";
        }

        // --- Inner CSS ---
        if( !empty( $this->strInnerCSSContent ) ){
            echo "<style type='text/css'>\n";
            echo $this->strInnerCSSContent;
            echo "</style>\n";
        }

        // --- Icon ---
        if( !empty( $this->strIcon ) ){
            //echo "<link rel='icon' type='image' href='".$this->strIcon."'/>\n";
            echo "<link rel='icon' href='".$this->strIcon."'/>\n";
        }

        // --- Keywords ---
        if( !empty( $this->strKeywords ) ){
            echo "<meta name='keywords' content='".$this->strKeywords."'/>\n";
        }
        
        // --- Description ---
        if( !empty( $this->strDescription ) ){
            echo "<meta name='description' content='".$this->strDescription."'/>\n";
        }

        echo "</head>\n";

        // --- Height ---
        if( !empty( $this->strHeight ) ){
            echo "<body style='height:100%;'>\n";
        }
        else{
            echo "<body>\n";
        }

        // --- JS files ---
        foreach ($this->strJSFiles as $strJSFile)
        {
            echo "<script src='".$strJSFile."'></script>\n";
        }

        // --- BODY content ---
        echo $this->strBodyContent;

        // --- End of Page ---
        echo "\n</body>\n";
        echo "</html>\n";
    }
}
?>