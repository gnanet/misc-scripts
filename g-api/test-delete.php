<?php

session_start();
require_once 'Google/Client.php';
require_once 'Google/Service/Calendar.php';

$authconfigfile='';

if (isempty($authconfigfile) && file_exists($authconfigfile);) {
	echo '<b>You have to specify the JSON file created in https://console.developers.google.com as OAUTH "Client ID for web application"</b>';
	exit;
}

$client = new Google_Client();
$client->addScope('https://www.googleapis.com/auth/calendar');
$client->setApplicationName("Calendar");
$client->setAccessType('online');
$client->setRedirectUri('http://localhost/g-api/test.php');

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
  setcookie("param", "delete=".$_GET['delete']);
  header('Location: http://' . $_SERVER['HTTP_HOST'] . '/g-api/test.php');
  exit;
}

if (isset($_REQUEST['delete'])) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Google Calendar APIv3 delete event test implementation</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 1.24.1" />
</head>

<body>
<?php
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
?>
</body>
</html>
<?php
}
