<?php require_once("funcs.php.inc"); ?>
<?php
	$band = $db->escape_string($_GET["band"]);
	$mode = $db->escape_string($_GET["mode"]);
	$operator = $db->escape_string($_GET["operator"]);
	$date = $db->escape_string($_GET["date"]);
	$station = $db->escape_string($_GET["station"]);
	$call = $db->escape_string($_REQUEST["call"]);
	$satname = $db->escape_string($_GET["satellitename"]);
?>
<html>
<head>
<link rel="stylesheet" href="style.css" />
</head>
<body>
<?php 
$whereClause="WHERE ";
$searchDescription = "";
if ($band != "") {
	$whereClause = $whereClause . "band='$band' AND ";
	$searchDescription = $searchDescription . "$band, ";
}
if ($mode != "") {
	$whereClause = $whereClause . "(mode='$mode' OR mode IN (SELECT sourceText FROM modemap where displayText='$mode')) AND ";
	$searchDescription = $searchDescription . "$mode, ";
}
if ($operator != "") {
	$whereClause = $whereClause . "operator LIKE '$operator' AND ";
	$searchDescription = $searchDescription . "operator $operator, ";
}
if ($date != "") {
	$whereClause = $whereClause . "startTime >= DATE('$date') AND startTime < DATE_ADD(DATE('$date'), INTERVAL 1 DAY)  AND ";
	$searchDescription = $searchDescription . "on " . formatDate($date) . ", ";
}
if ($station != "") {
	$whereClause = $whereClause . "station LIKE '$station' AND ";
	$searchDescription = $searchDescription . "station $station, ";
}
if ($call != "") {
	$whereClause = $whereClause . "callsign LIKE '%$call%' AND ";
	$searchDescription = $searchDescription . "callsign $call, ";
}
if ($satname != "") {
	$whereClause = $whereClause . "satellitename LIKE '%$satname%' AND ";
}
$searchDescription = substr($searchDescription, 0, -2);
echo ("<h2>Log search: " . $searchDescription . "</h2>");
$whereClause = $whereClause . "TRUE ORDER BY startTime";

//echo ($whereClause);
plotCalls($whereClause); ?>
<p class="navlink"><a href="index.php">Back to overview</a></p>
</body>
</html>
