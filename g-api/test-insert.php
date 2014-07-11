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
	if (isset($_COOKIE["returnto"])) {
	  $returndirect = 'http://' . $_SERVER['HTTP_HOST'] . $_COOKIE['returnto'] . "?". $_COOKIE['param'];
	  setcookie("returnto", "", time()-3600);
	  setcookie("param", "", time()-3600);
	  header('Location: ' . filter_var($returndirect, FILTER_SANITIZE_URL));
	  exit;
	}
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
	<title>Google Calendar APIv3 insert event test implementation</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 1.24.1" />
</head>

<body>
<?php


echo '<div><div class="request">';
if (isset($authUrl)) {
  echo '<a class="login" href=' . $authUrl . ">Connect Me!</a><br>";

} else {
  echo '<a class="logout" href="?logout">Logout</a><br>'."\n\n";

	try {  
	 $event = new Google_Service_Calendar_Event();  
	 $event->setSummary('Buli - ' . date('Y-m-d H:i:s'));
	 $event->setLocation('Otthon');
	 $start = new Google_Service_Calendar_EventDateTime();
	 $start->setDateTime('2014-07-11T22:00:00.000+02:00');
	 $event->setStart($start);
	 $end = new Google_Service_Calendar_EventDateTime();
	 $end->setDateTime('2014-07-12T02:25:00.000+02:00');
	 $event->setEnd($end);
	
	 $createdEvent = $service->events->insert('primary', $event);   
	 echo "<p>Created event with ID: ".$createdEvent->getId()."<br>\n\n";
	 
	 $modevent = $service->events->get('primary', $createdEvent->getId());
	 $modevent->setSummary('Buli - ' . date('Y-m-d H:i:s').' - '.$createdEvent->getId());
	 
	 
	 echo "<p>Summary: ".$createdEvent->getSummary()."<br>\n\n";
	 $updatedEvent = $service->events->update('primary', $modevent->getId(), $modevent);
	 // Print the updated date.
	 echo "<p>Summary updated: ".$updatedEvent->getSummary()."<br>\n\n";
	 echo "<p>update at: ".$updatedEvent->getUpdated()."<br>\n\n";
	 echo 'HTML link: <a href="' . $createdEvent->getHtmlLink() . '">' . $createdEvent->getHtmlLink() . "</a><br>\n\n";
	 echo "</p>";
	 echo '<a class="logout" href="http://localhost/g-api/test-delete.php?delete='.$createdEvent->getId().'">Delete '. $createdEvent->getSummary() ."</a><br>\n\n";
	 $file = 'events.html';
	 $dellink = 'edit <a href="' . $createdEvent->getHtmlLink() . '">' . $createdEvent->getId() . "</a> | ".'delete  <a href="http://localhost/g-api/test-delete.php?delete='.$createdEvent->getId().'">'.$createdEvent->getId()."</a> | ".'display  <a href="http://localhost/g-api/test-display.php?eid='.$createdEvent->getId().'">'.$createdEvent->getId()."</a><br>\n";
	 file_put_contents($file, $dellink, FILE_APPEND | LOCK_EX);
	 echo " <pre>\n";
	 print_r($createdEvent);
	 echo "</pre>\n";
	}

	catch (Exception $ex) {
	  die($ex->getMessage());
	}
};
echo '</div>';
?>
</body>
</html>
