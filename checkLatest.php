<?php
require_once("funcs.php.inc");
$sourceId = intval($_REQUEST["sourceId"]);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
$res = mysql_db_query($dbName, "SELECT MAX(lastModified) FROM log WHERE sourceId=$sourceId;") or die (mysql_error());
if ($arr = mysql_fetch_array($res))
	echo ($arr[0]);
else
	echo "2000-01-01 00:00:00";
?>
