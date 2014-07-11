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

// If we have a code back from the OAuth 2.0 flow, we need to exchange that
// with the authenticate() function. We store the resultant access token
// bundle in the session, and redirect to this page.

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();

  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
  exit;
}

// If we have an access token, we can make requests, else we generate an
// authentication URL.

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
	if (isset($_COOKIE["returnto"])) {
	  if (isset($_COOKIE["param"])) {
	  $returndirect = 'http://' . $_SERVER['HTTP_HOST'] . $_COOKIE['returnto'] . "?". $_COOKIE['param'];
	  setcookie("returnto", "", time()-3600);
	  setcookie("param", "", time()-3600);
	  } else {
	  $returndirect = 'http://' . $_SERVER['HTTP_HOST'] . $_COOKIE['returnto'];
	  setcookie("returnto", "", time()-3600);
	  }
	  header('Location: ' . filter_var($returndirect, FILTER_SANITIZE_URL));
	  exit;
	}
} else {
  $authUrl = $client->createAuthUrl();
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
if (isset($authUrl)) {
  echo '<a class="login" href=' . $authUrl . ">Connect Me!</a><br>\n\n";
	if (isset($_COOKIE["returnto"])) {
	  if (isset($_COOKIE["param"])) {
		$returndirect = 'http://' . $_SERVER['HTTP_HOST'] . $_COOKIE['returnto'] . "?". $_COOKIE['param'];
		echo "<b>After successful connection you will be returned to:".$returndirect."</b><br>\n\n";
	  } else {
		$returndirect = 'http://' . $_SERVER['HTTP_HOST'] . $_COOKIE['returnto'];
		echo "<b>After successful connection you will be returned to:".$returndirect."</b><br>\n\n";
	  }
	  }
} else {
	echo '<a class="logout" href="?logout">Logout</a><br>'."\n\n";
	echo "<b>You are logged in, but no action was requested</b><br>\n\n";
	echo '<a href="index.php">Back to index</a><br>'."\n\n";
};
echo '</div>';


