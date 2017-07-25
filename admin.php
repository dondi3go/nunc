<?php
//if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
ini_set('display_errors', 'On');
ini_set('error_reporting', 'E_ALL');

require_once(__DIR__.'/nunc/main/BasePage.php');
require_once(__DIR__.'/nunc/main/Layout.php');
require_once(__DIR__.'/nunc/main/Icon.php');
require_once(__DIR__.'/nunc/more/Picture.php');

require_once(__DIR__.'/nunc/more/Authentication.php');
require_once(__DIR__.'/nunc/more/AuthenticationUI.php');
require_once(__DIR__.'/nunc/more/AuthenticationLogger.php');
require_once(__DIR__.'/nunc/more/SecurityUI.php');
require_once(__DIR__.'/nunc/more/DebugUI.php');
require_once(__DIR__.'/nunc/more/SeoUI.php');
require_once(__DIR__.'/nunc/more/DBViewUI.php');

require_once(__DIR__.'/nunc/nubs/VisitsUI.php');
require_once(__DIR__.'/nunc/nubs/NotesUI.php');
require_once(__DIR__.'/nunc/nubs/EventsUI.php');
require_once(__DIR__.'/nunc/nubs/BookmarksUI.php');
require_once(__DIR__.'/nunc/nubs/QuotesUI.php');
require_once(__DIR__.'/nunc/nubs/RecipesUI.php');
require_once(__DIR__.'/nunc/nubs/BlogUI.php');
require_once(__DIR__.'/nunc/nubs/RSSChannelsUI.php');
require_once(__DIR__.'/nunc/nubs/PlacesUI.php');

require_once(__DIR__.'/nunc/Config.php');


function getMiscUI()
{
    $ui1 = new StackDisplay();
    $ui1->add(new UserSecurityUI());
    $ui1->add(new DomainSecurityUI());
    $ui1->add(new NuncSecurityUI());

    $ui = new MidNavBar();
    $ui->addLeftItem($ui1, "security", Icon::EYE);
    $ui->addLeftItem(new DebugUI(), "debug", Icon::BUG);
    $ui->addLeftItem(new SeoUI(), "s e o", Icon::SEARCH);

    return $ui;
}

// SET UP PAGE
$page = new BasePage();
$page->setTitle("Admin");
$page->setHeight("100%");
$page->setIcon("favicon.ico");
$page->enforceHTTPS();
$page->addBodyContent("<div class='container'><br/>\n");


// SET UP AUTHENTICATION
$auth = new Authentication();
$logger = new AuthenticationMailLogger("ericpain@free.fr", false, true, false);
$auth->setLogger($logger);
$auth->addUser("user", "toto");

// DATA STORE FOLDER
$storeDir = Config::data_store_folder;
$shareDir = Config::data_share_folder;

if(!$auth->isUserConnected())
{
    // Layout when not connected

    // LOG VISIT
    $visits = new VisitLogger( $storeDir."/visitsdb.xml" );
    $visits->logVisit("admin");

    $page->addBodyContent("<div class='col-md-offset-4 col-md-4'>\n");
    
    // Image
    $picture = new Picture("https://ericpain.fr/media/cover02.jpg");
    $picture->display($page);
    
    // Authentication
    $connectUI = new ConnectUI($auth);
    $connectUI->setUILabels("", "", "&nbsp;");
    $connectUI->display($page);

    $page->addBodyContent("</div>\n");

}
else
{
	// Visit Helper configuration
	VisitHelper::addFriendlyFullIp("178.16.174.130");
	VisitHelper::addFriendlyFullIp("78.194.188.192");
	VisitHelper::addFriendlyIpPrefix("46.246.3"); // VPN
	VisitHelper::addFriendlyIpPrefix("46.246.4");
	VisitHelper::addFriendlyIpPrefix("46.246.5");
	VisitHelper::addFriendlyIpPrefix("46.246.6");
	VisitHelper::addFriendlyUserAgent("Mozilla/5.0 (iPhone; CPU iPhone OS 9_3_2 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13F69 Safari/601.1");

	// Layout when connected

    $homeUI = new BasicDisplay("Home");
    $notesUI = NotesUIFactory::CreateFullUI( $storeDir."/notesdb.xml" );
    $blogUI = BlogUIFactory::CreateFullUI( $storeDir."/blogdb.xml", $shareDir."/blog/" );
    $newsUI = RSSChannelsUIFactory::CreateFullUI( $storeDir."/newsdb.xml" );
	$bookmarksUI = BookmarksUIFactory::CreateFullUI( $storeDir."/bookmarksdb.xml" );
	$eventsUI = EventsUIFactory::CreateFullUI( $storeDir."/eventsdb.xml" );
    $placesUI = PlacesUIFactory::CreateFullUI( $storeDir."/placesdb.xml" );
	$recipesUI = RecipesUIFactory::CreateFullUI( $storeDir."/recipesdb.xml" );
	$quotesui = QuotesUIFactory::CreateFullUI( $storeDir."/quotesdb.xml" );
	$trafficUI = VisitsUIFactory::createFullUI( $storeDir."/visitsdb.xml" );
	$miscUI = getMiscUI();

    $userUI = new StackDisplay();
    $userUI->add(new ConnectionUI($auth));
    $userUI->add(new DisconnectUI($auth));

	$topNavBar = new TopNavBar();
	$topNavBar->addLeftItem( $homeUI, "HOME", Icon::HOME );
	$topNavBar->addLeftItem( $bookmarksUI, "BOOKMARKS", Icon::BOOKMARK );
    $topNavBar->addLeftItem( $blogUI, "BLOG", Icon::PEN );
    $topNavBar->addLeftItem( $newsUI, "NEWS", Icon::BULLHORN );
	$topNavBar->addLeftItem( $notesUI, "NOTES", Icon::PIN );
	$topNavBar->addLeftItem( $eventsUI, "EVENTS", Icon::CALENDAR );
    $topNavBar->addLeftItem( $placesUI, "PLACES", Icon::MARKER );
	$topNavBar->addLeftItem( $recipesUI, "RECIPES", Icon::GRAIN );
	$topNavBar->addLeftItem( $quotesui, "QUOTES", Icon::BOOK );
	$topNavBar->addLeftItem( $trafficUI, "TRAFFIC", Icon::STATS );
	$topNavBar->addLeftItem( $miscUI, "MISC", Icon::WRENCH );
	$topNavBar->addRightItem( $userUI, "LOG OUT", Icon::USER );
	$topNavBar->display($page);
}

$page->addBodyContent("</div>\n"); // container

// SHOW PAGE
$page->flush();

?>