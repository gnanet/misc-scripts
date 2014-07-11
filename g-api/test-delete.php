<?php
error_reporting(E_ALL);

session_start();
require_once 'Google/Client.php';
require_once 'Google/Service/Calendar.php';


$client = new Google_Client();
$client->addScope('https://www.googleapis.com/auth/calendar');
$client->setApplicationName("Calendar");
$client->setAccessType('online');
$client->setRedirectUri('http://localhost/g-api/test.php');

if (isset($_SESSION['token'])) {
 $client->setAccessToken($_SESSION['token']);
}

$client->setAuthConfigFile('api-config-web.json');

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
  setcookie("param", "delete=".$_GET['delete']);
  header('Location: http://' . $_SERVER['HTTP_HOST'] . '/g-api/test.php');
  exit;
}

if (isset($_REQUEST['delete'])) {
	echo '<div><div class="request">';
	echo '<a class="logout" href="?logout">Logout</a><br>'."\n\n";

	try {  
	 $deletedevent = $service->events->delete('primary', $_GET['delete']);
	}

	catch (Exception $ex) {
	  echo "<p><b>Error occured</b><br>\n<pre>\n";
	  die($ex->getMessage());
	  echo "</pre></p>";
	}

	
	echo "<p>Removed event</p>\n\n";
	echo '<a href="index.php">Back to index</a><br>'."\n\n";
	echo "</div>";
}


