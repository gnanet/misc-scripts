<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Google Calendar APIv3 test implementation</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 1.24.1" />
</head>

<body>
<?php
$yesterday  = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
echo date("Y-m-d\TH:i:sP", $yesterday)."<br>\n";
?>
	<ul>
	<li><a href="test.php">Simply Login / logout</a>
	<li><a href="test-list.php">List todays events</a>
	</ul>
<br>
<p>Events recorded by test-insert.php<br><br>
<?php
$eventsrecorded = file_get_contents('events.html');
echo $eventsrecorded."\n<br>\n";
?>
</p>
</body>
</html>
