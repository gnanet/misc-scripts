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
	  } else {
	  $returndirect = 'http://' . $_SERVER['HTTP_HOST'] . $_COOKIE['returnto'] . "?". $_COOKIE['param'];
	  setcookie("returnto", "", time()-3600);
	  setcookie("param", "", time()-3600);
	  }
	  header('Location: ' . filter_var($returndirect, FILTER_SANITIZE_URL));
	  exit;
	}
} else {
  $authUrl = $client->createAuthUrl();
}

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


