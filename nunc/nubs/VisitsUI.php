<?php
require_once(__DIR__.'/../more/DBViewUI.php');
require_once(__DIR__.'/../more/DBEditUI.php');
require_once(__DIR__.'/../more/DBDataUI.php');
require_once(__DIR__.'/../main/Icon.php');
require_once(__DIR__.'/../nubs/Visits.php');

function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function contains($haystack, $needle)
{
    if(strpos($haystack, $needle)!==false) // returns false or an int
    {
        return true;
    }
    return false;
}

$FRIENDLY_FULL_IPS = array();
$FRIENDLY_IP_PREFIXES = array();
$FRIENDLY_USER_AGENTS = array();

class VisitHelper
{
    // Bots user-agents signatures
    private static $bots_useragents = array( 
        "Googlebot", "BlogSearch", "Moreoverbot", "BlogPulse", "JakartaCommons", 
        "RomeClient", "AboutUsBot", "GoogleBot", "PHPCrawl", "ThumbnailAgent", "archive.org",
        "voilabot", "Yandex", "msnbot", "Java/", "Wikio", "MagpieRSS", "PEARHTTP", "Yahoo!Slurp",
        "R6_CommentReader", "Exabot", "R6_FeedFetcher", "Drupal", "validator.w3.org", "FairShare",
        "relevantnoise", "seoprofiler", "TinEye", "123peoplebot", "webcollage", "catchbot", 
        "DeepnetExplorer", "BabyaDiscoverer", "Blogshares", "jaxified", "majestic12", "TwinglyRecon",
        "bingbot",  "plukkie", "OrangeBot", "feedfetcher", "zinepal", "Disallow:/", "Allow:/", "ezooms", 
        "radian6", "Mediapartners", "kindsight", "Apple-PubSub", "SISTRIXCrawler", "SiteExplorer",
        "bnf.fr_bot", "DomainCrawler", "DCPbot", "Baiduspider", "MyNutchSpider", "AhrefsBot",
        "SpeedySpider", "ltbot", "EbuzzingFeedBot", "ia_archiver", "moz.com", "Qwantify",
        "LSSRocketCrawler", "DNSdelveHTTPtester", "YahooCacheSystem", "BingPreview", "AddThis", 
        "GoogleWebPreview", "VoilaBotCollector", "favicon", "Google-Site-Verification",
        "aiHitBot", "Screaming Frog", "DNSdelve", "EbuzzingFeed", "DotBot", "nbot", "SISTRIX",
        "Apache-HttpClient", "Slurp", "TweetedTimes", "linkfluence", "TweetmemeBot", "Twitterbot",
        "PaperLiBot", "ubermetrics", "MetaURI", "RebelMouse", "Mozilla/5.0 ()", "facebookexternalhit",
        "Crowsnest", "EventMachine", "Google-HTTP-Java-Client", "Gigabot", "LinkedInBot", 
        "SMRF URL Expander", "EveryoneSocialBot", "Jakarta", "support@digg.com", "pear.php.net",
        "LivelapBot", "imrbot", "Embedly", "Jetslide", "WASALive-Bot", "newsme", "Python",
        "Twurly", "jack", "PycURL", "ShowyouBot", "InAGist", "spyder/Nutch", "LongURL", "Twikle",
        "proximic", "bixo", "websays", "Ruby", "OpenHoseBot", "QuerySeekerSpider", "Digg",
        "Antidot", "Feedly", "Grapeshot", "Abonti", "zitebot", "Kimengi", "PercolateCrawler",
        "Genieo", "nutch", "Netvibes", "Icarus", "SimplePie", "SimpleCrawler", "bitlybot",
        "ADmantX", "Apercite", "OpenLinkProfiler", "eCairn", "Dispatch", "Lipperhey", "libwww-perl",
        "linkdexbot", "memoryBot", "BLEXBot", "Wotbox", "psbot", "DomainAppender", "xovibot",
        "worldwebheritage", "Climatebot", "Dazoobot", "BUbiNG", "SEOkicks", "MixBot", "tweetedtimes",
        "mojeek", "adsbot", "MegaIndex", "semrush", "cognitiveseo", "Applebot", "YoonoCrawler",
        "meanpath", "MXT/Nutch", "trove.com", "SeznamBot", "DuckDuckGo-Favicons-Bot", "digincore",
        "websurvey", "CheckMarkNetwork", "dataprovider.com", "Validator.nu" );

    private static $bots_prefixes = array( 
        "64.79.100.", // Continuum Data Center ???
        "64.74.215."  // Internap ???
        );

    private static $bots_referers = array(
        "pizza" // well ... we see that
        );

    //
    // Returns full url of service giving details about an ip address
    //
    public static function getDetailUrl($ip)
    {
        return "http://www.iplocationfinder.com/".$ip;
    }

    //
    // Tells if the visitor is identified as a bot
    // A bot is supposed to be friendly
    // If signs of hostility are found then this method returns false
    //
    public static function isBot($visit)
    {
        // A regular bot is not supposed to be hostile
        $note = $visit->getPropertyValue(Visit::Note);
        if( contains($note, "error") )
        {
            return false;
        }

        // Compare with list of user agent bots signature
        $useragent = $visit->getPropertyValue(Visit::UserAgent);
        foreach(self::$bots_useragents as $bot_useragent)
        {
            if( contains($useragent, $bot_useragent) )
            {
                return true;
            }
        }

        // Compare with bots ip prefixes
        $ip = $visit->getPropertyValue(Visit::Ip);
        foreach(self::$bots_prefixes as $prefix)
        {
            if( startsWith($ip, $prefix) )
            {
                return true;
            }
        }

        // Compare with bots referers signature
        $referer = $visit->getPropertyValue(Visit::Referer);
        foreach(self::$bots_referers as $word)
        {
            if( contains($referer, $word) )
            {
                return true;
            }
        }

        return false;
    }

    //
    // Identify some visits as friendly
    //
    public static function addFriendlyFullIp($ip)
    {
        $GLOBALS['FRIENDLY_FULL_IPS'][] = $ip;
    }

    public static function addFriendlyIpPrefix($prefix)
    {
        $GLOBALS['FRIENDLY_IP_PREFIXES'][] = $prefix;
    }

    public static function addFriendlyUserAgent($useragent)
    {
        $GLOBALS['FRIENDLY_USER_AGENTS'][] = $useragent;
    }

    //
    //  Tells if the visit is identified as friendly
    //
    public static function isFriendly($visit)
    {
        $ip = $visit->getPropertyValue(Visit::Ip);

        // Check full ip
        $fullIps = $GLOBALS['FRIENDLY_FULL_IPS'];
        foreach($fullIps as $fullIp)
        {
            if( $ip == $fullIp )
            {
                return true;
            }
        }
        
        // Check gainst ip prefixes
        $prefixes = $GLOBALS['FRIENDLY_IP_PREFIXES'];
        foreach($prefixes as $prefix)
        {
            if( startsWith($ip, $prefix) )
            {
                return true;
            }
        }

        // Check full user agent
        $useragent = $visit->getPropertyValue(Visit::UserAgent);
        $agents = $GLOBALS['FRIENDLY_USER_AGENTS'];
        foreach($agents as $agent)
        {
            if( $agent == $useragent )
            {
                return true;
            }
        }

        return false;
    }

    //
    //
    //
    public static function isHostile($visit)
    {
        // Check empty user agent
        $useragent = $visit->getPropertyValue(Visit::UserAgent);
        if($useragent == "")
        {
            return true;
        }

        // No one is supposed to access admin page except admin itself
        $note = $visit->getPropertyValue(Visit::Note);
        if( contains($note, "admin") )
        {
            return true;
        }

        // Check if log is made by error.php file
        $note = $visit->getPropertyValue(Visit::Note);
        if( contains($note, "error") )
        {
            return true;
        }
        return false;
    }

    //
    //
    //
    const BOT = "bot";
    const FRIENDLY = "friendly";
    const HOSTILE = "hostile";
    const OTHER = "other";

    public static function getProfile($visit)
    {
        if( self::isFriendly($visit) )
            return self::FRIENDLY;
        if( self::isBot($visit) )
            return self::BOT;
        if( self::isHostile($visit) )
            return self::HOSTILE;
        return self::OTHER;
    }

    //
    public static function getOSFamily($useragent)
    {
        if( contains($useragent, "Windows") )
            return "Windows";
        if( contains($useragent, "Macintosh") )
            return "OS X";
        if( contains($useragent, "iPhone") )
            return "iOS";
        if( contains($useragent, "Linux") )
            return "Linux";
        return "unknown";
    }
    
    //
    public static function getBrowserFamily($useragent)
    {
        if( contains($useragent, "Firefox") )
            return "Firefox";
        if( contains($useragent, "Safari") )
            return "Safari";
        if( contains($useragent, "MSIE") )
            return "MSIE";
        return "unknown";
    }

    //
    public static function getOSAndBrowser($useragent)
    {
        $osfamily = self::getOSFamily($useragent);
        $browserfamily = self::getBrowserFamily($useragent);
        if($osfamily == "unknown" or $browserfamily == "unknown")
            return $useragent;
        else
            return $osfamily." / ".$browserfamily;
    }

    //
    public static function getShortUserAgent($useragent)
    {
        return trim($useragent, "Mozilla/5.0 ");
    }
}

class BriefVisitUI implements IObjectUI
{
    public function getStrUI($visit)
    {
        $timestamp = $visit->getPropertyValue(Visit::Timestamp);
        $ip = $visit->getPropertyValue(Visit::Ip);
        $note = $visit->getPropertyValue(Visit::Note);
        $referer = $visit->getPropertyValue(Visit::Referer);
        $useragent = $visit->getPropertyValue(Visit::UserAgent);
        
        $url = VisitHelper::getDetailUrl($ip);

        $str = "";

        // Bot / Friendy / Hostile / Other
        $profile = VisitHelper::getProfile($visit);
        switch($profile)
        {
            case VisitHelper::BOT:      $str.= Icon::INFO; break;
            case VisitHelper::FRIENDLY: $str.= Icon::OK; break;
            case VisitHelper::HOSTILE:  $str.= Icon::ERROR; break;
            case VisitHelper::OTHER:    $str.= Icon::STAR; break;
        }

        // Date - ip - profile
        $str.= " ".date("Y.m.d H:i:s - ", $timestamp)."<b><a href='".$url."' target='blank'>".$ip."</a></b>";

        $str.= "<br/>";

        // Note
        if( contains($note, 'error') )
            $str.= "<span class='text-danger small'><b>".$note."</b></span><br/>";
        else
            $str.= "<span class='text-muted small'>".$note."</span><br/>";
        
        // Referer
        if( strlen($referer) == 0 )
            $str.= "<span class='text-danger small'><b>no referer</b></span><br/>";
        else
            $str.= "<span class='text-muted small'>".$referer."</span><br/>";

        // User Agent
        if( strlen($useragent) == 0 )
            $str.= "<span class='text-danger small'><b>no user agent</b></span><br/>";
        else
        {
            $osandbrowser = VisitHelper::getOSAndBrowser($useragent);
            $shortuseragent = VisitHelper::getShortUserAgent($useragent);
            $str.= "<span class='text-muted small'>".$osandbrowser."</span><br/>";
            $str.= "<span class='text-muted small hidden-xs hidden-sm'>".$shortuseragent."<br/></span>";
        }

        $str.="<br/>";

        return $str;
    }
}

//
//
//
interface VisitsStatAction
{
    const CLEAR_BOTS      = 'visits_stat_clear_bots';
    const CLEAR_FRIENDLY  = 'visits_stat_clear_friendly';
    const CLEAR_HOSTILE   = 'visits_stat_clear_hostile';
    const CLEAR_OTHERS    = 'visits_stat_clear_others';
}


class VisitsStatUI implements IDisplayable
{
    private $collection;
    private $filename;      // url of xml file

    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    private function removeProfile($profileToRemove)
    {
        $col = $this->collection;
        for( $i=0; $i<$col->getObjectCount();)
        {
            $visit = $col->getObjectByIndex($i);
            $profile = VisitHelper::getProfile($visit);
            if( $profile == $profileToRemove )
            {
                $col->removeObject($visit); // DO NOT ITERATE
            }
            else
            {
                $i++; // ITERATE
            }
        }
        $this->saveDB();
    }

    private function handleAction()
    {
        if( isset($_POST[VisitsStatAction::CLEAR_BOTS]) )
        {
            $this->removeProfile(VisitHelper::BOT);
        }
        else if( isset($_POST[VisitsStatAction::CLEAR_FRIENDLY]) )
        {
            $this->removeProfile(VisitHelper::FRIENDLY);
        }
        else if( isset($_POST[VisitsStatAction::CLEAR_HOSTILE]) )
        {
            $this->removeProfile(VisitHelper::HOSTILE);
        }
        else if( isset($_POST[VisitsStatAction::CLEAR_OTHERS]) )
        {
            $this->removeProfile(VisitHelper::OTHER);
        }
    }

    public function display(IPage $page)
    {
        $str = "";
        if( $this->loadDB() )
        {
            $this->handleAction();
            $str .= $this->displayStats();
        }
        else
        {
            $str .= $this->displayMessage("no data");
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

    public function displayStats()
    {
        // Counters 
        $bCount = 0; // bots
        $fCount = 0; // friendly
        $hCount = 0; // hostile
        $oCount = 0; // others

        $imax = $this->collection->getObjectCount();
        for( $i=0; $i<$imax; $i++)
        {
            $visit = $this->collection->getObjectByIndex($i);

            $profile = VisitHelper::getProfile($visit);
            switch($profile)
            {
                case VisitHelper::BOT: $bCount++; break;
                case VisitHelper::FRIENDLY: $fCount++; break;
                case VisitHelper::HOSTILE: $hCount++; break;
                case VisitHelper::OTHER: $oCount++; break;
            }
        }

        $total = $bCount+$fCount+$hCount+$oCount;

        $strClassEnabled = "btn pull-right btn-default btn-sm";

        $bButton = "<button class='".$strClassEnabled."' type='submit' value='novalue' name='".VisitsStatAction::CLEAR_BOTS."'>".Icon::CROSS." clear</button>";
        $fButton = "<button class='".$strClassEnabled."' type='submit' value='novalue' name='".VisitsStatAction::CLEAR_FRIENDLY."'>".Icon::CROSS." clear</button>";
        $hButton = "<button class='".$strClassEnabled."' type='submit' value='novalue' name='".VisitsStatAction::CLEAR_HOSTILE."'>".Icon::CROSS." clear</button>";
        $oButton = "<button class='".$strClassEnabled."' type='submit' value='novalue' name='".VisitsStatAction::CLEAR_OTHERS."'>".Icon::CROSS." clear</button>";

        $bIcon = Icon::INFO;
        $fIcon = Icon::OK;
        $hIcon = Icon::ERROR;
        $oIcon = Icon::STAR;

        // Show results
        $str.= "<form method='post'>\n";
        $str.= "<table class='table table-condensed'>\n";
        $str.= "<tr><td><p class='text-muted'>".$bIcon." bots</p></td>\n";
        $str.= "<td><b>".$bCount."</b></td><td>".$bButton."</td></tr>\n";
        $str.= "<tr><td><p class='text-muted'>".$fIcon." friendly</p></td>\n";
        $str.= "<td><b>".$fCount."</b></td><td>".$fButton."</td></tr>\n";
        $str.= "<tr><td><p class='text-muted'>".$hIcon." hostile</p></td>\n";
        $str.= "<td><b>".$hCount."</b></td><td>".$hButton."</td></tr>\n";
        $str.= "<tr><td><p class='text-muted'>".$oIcon." others</p></td>\n";
        $str.= "<td><b>".$oCount."</b></td><td>".$oButton."</td></tr>\n";
        $str.= "<tr><td><p class='text-muted'>TOTAL</p></td>\n";
        $str.= "<td><b>".$total."</b></td><td></td></tr>\n";
        $str.= "</table>\n";
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

class VisitsUIFactory
{
    public static function createViewUI($filename)
    {
        $ui = new DBViewUI();
        $ui->setCollection(new Visits());
        $ui->setObjectUI(new BriefVisitUI());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createEditUI($filename)
    {
        $ui = new DBEditUI();
        $ui->setCollection(new Visits());
        $ui->setObjectUI(new BriefVisitUI());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createStatUI($filename)
    {
        $ui = new VisitsStatUI();
        $ui->setCollection(new Visits());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createDataUI($filename)
    {
        $ui = new DBDataUI();
        $ui->setCollection(new Visits());
        $ui->setFilename($filename);
        return $ui;
    }

    public static function createFullUI($filename)
    {
        $viewUI = VisitsUIFactory::createViewUI($filename);
        $editUI = VisitsUIFactory::createEditUI($filename);
        $statUI = VisitsUIFactory::createStatUI($filename);
        $dataUI = VisitsUIFactory::createDataUI($filename);

        $ui = new MidNavBar();
        $ui->addLeftItem($viewUI, "view", Icon::EYE);
        $ui->addLeftItem($editUI, "edit", Icon::PEN);
        $ui->addLeftItem($statUI, "stat", Icon::STATS);
        $ui->addRightItem($dataUI, "data", Icon::CLOUD);
        return $ui;
    }
}


?>