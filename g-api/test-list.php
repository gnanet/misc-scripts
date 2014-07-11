<?php

session_start();
require_once 'Google/Client.php';
require_once 'Google/Service/Calendar.php';

// Specify your web client ID's downloadable JSON file
$authconfigfile='';
if (isempty($authconfigfile) && file_exists($authconfigfile);) {
	echo '<b>You have to specify the JSON file created in https://console.developers.google.com as OAUTH "Client ID for web application"</b>';
	exit;
}

$client = new Google_Client();
$client->addScope('https://www.googleapis.com/auth/calendar');
$client->setApplicationName("Calendar");
$client->setAccessType('online');

if (isset($_SESSION['token'])) {
 $client->setAccessToken($_SESSION['token']);
}

$client->setAuthConfigFile($authconfigfile);

$service = new Google_Service_Calendar($client);

// If we're logging out we just need to clear our local access token
if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
}

// If we have an access token, we can make requests, else we generate an
// authentication URL.

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
} else {
  setcookie("returnto", $_SERVER['PHP_SELF']);
  header('Location: http://' . $_SERVER['HTTP_HOST'] . '/g-api/test.php');
  exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Google Calendar APIv3 list events test implementation</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 1.24.1" />
</head>

<body>
<?php
	echo '<div><div class="request">';
	echo '<a class="logout" href="?logout">Logout</a><br>'."\n\n";

	try {
		$yesterday  = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
		$listedEvents = $service->events->listEvents('primary', array('timeMin'=>date("Y-m-d\TH:i:sP", $yesterday), 'timeMax'=>date("Y-m-d\TH:i:sP")) 
		);
	}

	catch (Exception $ex) {
	  echo "<p><b>Error occured</b><br>\n<pre>\n";
	  die($ex->getMessage());
	  echo "</pre></p>";
	}

	echo "<h2>Calendar events from yesterday until now:</h2><br>\n";

echo "<table>\n";
echo "<tr><th>EventID</th><th>Summary (click to view)</th><th>From</th><th>To</th><th>Actions</th></tr>\n";

  foreach ($listedEvents->getItems() as $listedEvent) {
    echo "<tr>\n";
    echo '<td>'.$listedEvent->getId().'</td><td><a href="/g-api/test-display.php?eid='.$listedEvent->getId().'">'. $listedEvent->getSummary(). '</a></td><td>'.$listedEvent->start->getDateTime().'</td><td>'.$listedEvent->end->getDateTime().'</td>';
    echo '<td><a href="/g-api/test-delete.php?delete='.$listedEvent->getId().'">delete</a></td>'." \n";
    echo "</tr>\n";
  }
echo "</table>\n<br>\n";

	echo '<a href="index.php">Back to index</a><br>'."\n\n";
	echo "</div>";
?>
</body>
</html>
