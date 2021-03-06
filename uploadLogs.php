<?php
require_once("funcs.php.inc");
$webBase = "";

$res = $db->query("SELECT id FROM sources WHERE `default`=1;");
$arr = $res->fetch_assoc();
$sourceId=intval($arr["id"]);

while (true) {
	echo ("Getting latest remote server state...\n");
	$handle = fopen("$webBase/checkLatest.php?sourceId=$sourceId", "r") or die ("Error opening check latest file");
	$contents = fread($handle, 8192);// or die ("Error reading check latest file");
	fclose($handle);
	$lastModified = $contents;
	echo ("Last QSO on server was number: $lastModified\n");
	$sqlRes = $db->query"SELECT MAX(lastModified) AS maxMod FROM log;") or die ("Error querying local DB: " . $db->error);
	$sqlArr = $sqlRes->fetch_assoc();
	$lastLocal = $sqlArr["maxMod"];
	echo ("Last local QSO modified at: $lastLocal\n");
	echo ("Beginning uploads...\n");
	$sqlRes = $db->query("SELECT * FROM log WHERE lastModified >= ('$lastModified' - interval 5 minute);") or die ("Error querying table " . $db->error);

	while ($sqlArr = $sqlRes->fetch_assoc())
		{
			$id = $sqlArr["id"];
			$sourceId = $sqlArr["sourceId"];
			$lastModified = urlencode($sqlArr["lastModified"]);
			$startTime = urlencode($sqlArr["startTime"]);
			$endTime = urlencode($sqlArr["endTime"]);
			$callsign = urlencode($sqlArr["callsign"]);
			$operator = urlencode($sqlArr["operator"]);
			$station=urlencode($sqlArr["station"]);
			$band = $sqlArr["band"];
			$mode = getModeForUpload($sqlArr["mode"]);
			$freq = $sqlArr["frequency"];
			$reportTx = urlencode($sqlArr["reportTx"]);
			$reportRx = urlencode($sqlArr["reportRx"]);
			$locator = urlencode($sqlArr["locator"]);
			$notes = urlencode($sqlArr["notes"]);

			$url = "$webBase/uploadAnEntry.php?id=$id&sourceId=$sourceId&lastModified=$lastModified&startTime=$startTime&endTime=$endTime&callsign=$callsign&station=$station&operator=$operator&band=$band&mode=$mode&frequency=$freq&reportTx=$reportTx&reportRx=$reportRx&locator=$locator&notes=$notes";
			echo ("\n$url\n");
			$uploadFileHandle = fopen($url, "r") or die ("Error uploading QSO $primaryKey");
			fclose($uploadFileHandle);
		
		}

	echo ("Uploads complete!\n");
	echo ("Sleeping for 120 seconds...\n\n\n");
	sleep(120);
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
