<?php
$dbName = "arran2011";
$dataDbName = "arran2011data";

echo ("Connecting to local DB...\n");
mysqli_connect("localhost", "root", "");
$arr = mysql_fetch_array(mysql_db_query($dbName, "SELECT val from setup WHERE `key`='sourceId';"));
$sourceId=intval($arr[0]);

while (true) {

	echo ("Uploads complete!\n");
	echo ("Sleeping for 30 seconds...\n\n\n");

	$sourceQuery = "SELECT * FROM TABLE_HRD_CONTACTS_V01 WHERE COL_USER_DEFINED_0 IS NULL;";
	$sourceResultSet = mysql_db_query($dataDbName, $sourceQuery) or die ("Cannot get source data for sync");
	while ($sourceArray = mysql_fetch_array($sourceResultSet)) {
		echo("Syncing contact...\n");
		$band = $sourceArray["COL_BAND"];
		$freq = intval($sourceArray["COL_FREQ"]);
		$mode = $sourceArray["COL_MODE"];
		$operator = $sourceArray["COL_OPERATOR"];
		$rstRx = $sourceArray["COL_RST_RCVD"];
		$rstTx = $sourceArray["COL_RST_SENT"];
		$call = $sourceArray["COL_CALL"];
		$time = $sourceArray["COL_TIME_ON"];
		$primaryKey = $sourceArray["COL_PRIMARY_KEY"];

		$insertSql = "INSERT INTO log (sourceId, lastModified, startTime, endTime, callsign, operator, band, mode, frequency, reportTx, reportRx, serialSent, serialReceived, station) VALUES ($sourceId, now(), '$time', '$time', '$call', '$operator', '$band', '$mode', $freq, '$rstTx', '$rstRx', '', '', 'DATA');";
		
		echo ("$insertSql\n\n");
		mysql_db_query($dbName, $insertSql) or die ("Error inserting row: " . mysql_error());

		mysql_db_query($dataDbName, "UPDATE TABLE_HRD_CONTACTS_V01 SET COL_USER_DEFINED_0 = 1 WHERE COL_PRIMARY_KEY=$primaryKey;") or die ("Error updating source DB: " . mysql_error());
	}
	sleep(30);
}

function getModeForUpload($mode) {
	echo ("Mode is: $mode\n");
	if ($mode=="LSB")
		return "SSB";
	if ($mode=="USB")
		return "SSB";
	else
		return $mode;
}
?>
