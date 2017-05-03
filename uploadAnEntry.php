<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

echo ("Hello world\n");
require_once("funcs.php.inc");
flush();
$id = intval($_REQUEST["id"]);
$sourceId = intval($_REQUEST["sourceId"]);
$lastModified = mysql_escape_string($_REQUEST["lastModified"]);
$startTime = mysql_escape_string($_REQUEST["startTime"]);
$endTime = mysql_escape_string($_REQUEST["endTime"]);
$callsign = mysql_escape_string($_REQUEST["callsign"]);
$operator = mysql_escape_string($_REQUEST["operator"]);
$station = mysql_escape_string($_REQUEST["station"]);
$band = mysql_escape_string($_REQUEST["band"]);
$mode = mysql_escape_string($_REQUEST["mode"]);
$freq = mysql_escape_string($_REQUEST["frequency"]);
$reportTx = mysql_escape_string($_REQUEST["reportTx"]);
$reportRx = mysql_escape_string($_REQUEST["reportRx"]);
$locator = mysql_escape_string($_REQUEST["locator"]);
$notes = mysql_escape_string($_REQUEST["notes"]);

echo ("Hello world");
$sql = "SELECT COUNT(*) FROM log WHERE id=$id AND sourceId=$sourceId";
$sqlRes = mysql_db_query($dbName, $sql) or die (mysql_error());
$resultArr = mysql_fetch_array($sqlRes) or die (mysql_error());
if ($resultArr[0] == 0) {
	$sql = "INSERT INTO log (id, sourceId, lastModified, startTime, endTime, callsign, station, operator, band, 
		mode, frequency, reportTx, reportRx, locator, notes) VALUES ($id, $sourceId, '$lastModified', '$startTime', '$endTime', '$callsign', '$station',
		'$operator', '$band', '$mode', $freq, '$reportTx', '$reportRx', '$locator', '$notes')";
} else {
	$sql = "UPDATE log SET lastModified='$lastModified', startTime='$startTime', endTime='$endTime', callsign='$callsign', station='$station', operator='$operator', 
		band='$band', mode='$mode', frequency=$freq, reportTx='$reportTx', reportRx='$reportRx', locator='$locator', notes='$notes'
		WHERE id=$id AND sourceId=$sourceId;";
}

echo ($sql);
mysql_db_query($dbName, $sql) or die (mysql_error());
echo ("Good");
?>
