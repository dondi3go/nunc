<?php
require_once(__DIR__.'/nunc/nubs/Visits.php');

$errorCode = getenv("REDIRECT_STATUS");

// LOG VISIT
$logMessage = 'error '.$errorCode.' : '.$_SERVER['REQUEST_URI'];
$visits = new VisitLogger("./data/store/visitsdb.xml");
$visits->logVisit($logMessage);

// PAGE CONTENT
echo "<html><body>";
echo "<h1>error ".$errorCode."</h1>";
echo "</body></html>";

?>