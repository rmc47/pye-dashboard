<?php
// Dark blue: #356AA0
// Lighter: #a3bec5
// Title text: #6995c2
// Link: #356aa0
session_start();
mysql_connect("localhost", "root", "");
$dbName = "arran2016";

date_default_timezone_set('Etc/UTC');

function getAllBands() {
global $dbName;
$res = mysql_db_query($dbName, "SELECT DISTINCT log.band FROM log LEFT OUTER JOIN bandorder ON log.band=bandorder.band ORDER BY bandorder.order") or die ("Error getting bands: " . mysql_error());
$results = array();
$resultRow = 0;
while ($arr = mysql_fetch_array($res)) {
$results[$resultRow++] = $arr["band"];
}
return $results;
}

function getAllModes() {
global $dbName;
$res = mysql_db_query($dbName, "SELECT modes.displayText AS displayText FROM (SELECT DISTINCT(displayText) FROM modemap UNION SELECT DISTINCT(mode) FROM log WHERE mode NOT IN (SELECT sourceText FROM modemap) ORDER BY displayText) AS modes INNER JOIN modeorder ON modes.displayText=modeorder.mode ORDER BY modeorder.order") or die ("Error getting modes: " . mysql_error());
$results = array();
$resultRow = 0;
while ($arr = mysql_fetch_array($res)) {
$results[$resultRow++] = $arr["displayText"];
}
return $results;
}

function getBandModeCount($band, $mode) {
global $dbName;
$res = mysql_db_query($dbName, "SELECT cnt FROM bandmodestats WHERE band='$band' AND mode='$mode';") or die ("Error getting count: " . mysql_error());
if ($arr = mysql_fetch_array($res))
return $arr[0];
}

function getBandModeCountLive($band, $mode) {
global $dbName;
$res = mysql_db_query($dbName, "SELECT COUNT(*) FROM log LEFT JOIN modemap ON log.mode = modemap.sourceText WHERE band='$band' AND (mode='$mode' OR displayText='$mode');");
if ($arr = mysql_fetch_array($res))
return $arr[0];
else 
return 0;
}

function updateBandModeTable() {
global $dbName;
$modes = getAllModes();
$bands = getAllBands();
echo ("Updating bands table\n");
foreach ($modes as $mode) {
echo("Using mode: $mode\n");
foreach ($bands as $band) {
	$cnt = getBandModeCountLive($band, $mode);
	$sql = "INSERT INTO bandmodestats (band, mode, cnt) VALUES ('$band', '$mode', $cnt) ON DUPLICATE KEY UPDATE cnt=$cnt;";
	mysql_db_query($dbName, $sql) or die ("Error updating stats: " . mysql_error());
}
}
echo ("Done.\n");
}

function getTotalCount() {
global $dbName;
$res = mysql_db_query($dbName, "SELECT COUNT(*) FROM log;");
if ($arr = mysql_fetch_array($res))
return $arr[0];
else
return 0;
}

function getOperators() {
global $dbName;
$res = mysql_db_query($dbName, "SELECT operator, COUNT(*) FROM log GROUP BY operator ORDER BY COUNT(*) DESC;");
$results = array();
$resultRow = 0;
while ($arr = mysql_fetch_array($res))
$results[$resultRow++] = $arr["operator"];
return $results;
}

function getOperatorCount($operator) {
global $dbName;
$res = mysql_db_query($dbName, "SELECT qsos FROM operatorstats WHERE operator='$operator';");
if ($arr = mysql_fetch_array($res))
return $arr[0];
}

function getLastWorked($callsign) {
global $dbName;
$res = mysql_db_query($dbName, "SELECT operator, startTime FROM log WHERE callsign LIKE '%$callsign%' ORDER BY startTime DESC LIMIT 1;");
if ($arr = mysql_fetch_array($res))
return ($arr["startTime"]) . " by " . $arr["operator"];
}

function plotBandModesTable() {
$bands = getAllBands();
$modes = getAllModes();

$modeSums = array();
$grandTotal = 0;

?><table align="center" class="bandModesTableOutline"><tr><td><table class="bandModesTable"><tr class="bandModesTableHead"><td>&nbsp;</td><?php
// Plot the header row of modes
foreach ($modes as $mode) {
echo ("<td>" . formatMode($mode) . "</td>");
$modeSums[$mode] = 0;
}
echo ("<td style=\"font-weight: bold;\">TOTAL</td>");
echo ("<td style=\"font-weight: bold;\">Squares</td></tr>");
// Now do the bands
foreach ($bands as $band) {
	echo ("<tr><td class=\"bandModesTableHead\">$band</td>");
	$sum = 0;
	foreach ($modes as $mode) {
		$modeCount = getBandModeCount($band, $mode);
		$sum += $modeCount;
		$modeSums[$mode] += $modeCount;
		$grandTotal += $modeCount;
		echo ("<td><a href=\"bandMode.php?band=$band&mode=$mode\">$modeCount</a></td>");
	}
	echo ("<td style=\"font-weight: bold;\"><a href=\"bandMode.php?band=$band\">$sum</a></td>");
	echo ("<td><a href=\"squares.php?band=$band\">" . countSquaresWorked($band) . "</a></td>");
	echo ("</tr>");
}
// And the mode total row
echo ("<tr><td class=\"bandModesTableHead\" style=\"font-weight: bold;\">TOTAL</td>");
foreach ($modes as $mode) {
echo ("<td><a href=\"bandMode.php?mode=$mode\">" . $modeSums[$mode] . "</a></td>");
}
echo ("<td style=\"font-weight:bold;\">$grandTotal</td>");
echo ("</tr>");
// End of the total row

echo ("</table></td></tr></table>");
}

function getLatestFrequenciesArray() {
global $dbName;
$sql = "select frequency, startTime, log.station from log inner join (select MAX(id) id, station from log GROUP BY Station) latestIds on log.id=latestIds.id WHERE startTime > DATE_SUB(now(),INTERVAL 2 HOUR) ORDER BY station";
$result = mysql_db_query($dbName, $sql) or die("Error getting latest frequencies: " . mysql_error());

$latestFrequencies = array();
while ($resultArray = mysql_fetch_array($result)) {
$prettyFrequency = $resultArray["frequency"] / 1000000;
$latestFrequencies[$resultArray["station"]] = number_format($prettyFrequency, 3);
}
return $latestFrequencies;
}

function plotLatestFrequenciesTable() {
$latestFrequencies = getLatestFrequenciesArray();
?><table align="center" class="bandModesTableOutline"><tr><td><table class="bandModesTable"><tr class="bandModesTableHead"><td>Station</td><td>Freq</td></tr><?php
foreach (array_keys($latestFrequencies) as $station) {
echo ("<tr><td><a href=\"http://pi-$station:8000/raspi.m3u\" target=\"_blank\">$station</a></td><td>" . $latestFrequencies[$station] . "</td></tr>");
}
?></table></td></tr></table><?php
}

function plotOperatorsTable() {
$operators = getOperators();
?><table align="center" class="bandModesTableOutline"><tr><td><table class="bandModesTable"><tr class="bandModesTableHead">
<td>Operator</td><td>QSOs</td>
</tr>
<?php
foreach ($operators as $operator) {
echo ("<tr><td><a href=\"bandMode.php?operator=$operator\">".strtoupper($operator)."</a></td><td>" . getOperatorCount($operator) . "</td></tr>");
}
echo ("</tr></table></td></tr></table>");
}

function plotCalls($sqlWhere) {
global $dbName;

if (isset($_SESSION["showEdit"]) && $_SESSION["showEdit"])
$showEdit = true;
else
$showEdit = false;

$res = mysql_db_query($dbName, "SELECT * FROM log " . $sqlWhere) or die ("Error querying DB: " . mysql_error());
?><table class="logTableOutline"><tr><td><table class="logTable"><tr class="logTableHead"><td>Time</td><td>Date</td><td>Callsign</td><!--<td>Band</td>--><td>Frequency</td><td>Mode</td><td>Satellite</td><td>Worked&nbsp;by</td><td>Station</td>
<?php
if (isset($showEdit) && $showEdit) {
echo ("<td>Edit</td>");
}
?></tr><?php
$rowNum = 0;
while ($arr = mysql_fetch_array($res)) {
$rowClass = (($rowNum++ % 2) == 1) ? "oddRow" : "evenRow";
?>
<tr class="<?php echo $rowClass?>">
<td><?php echo formatTime($arr["startTime"]) ?></td>
<td><a href="bandMode.php?date=<?php echo substr ($arr["startTime"], 0, 11) ?>"><?php echo formatDate($arr["startTime"], "/") ?></a></td>
<td><a href="bandMode.php?call=<?php echo $arr["callsign"]?>"><?php echo $arr["callsign"]?></a></td>
<td><?php echo formatFrequency($arr["frequency"])?></td>
<td><a href="bandMode.php?mode=<?php echo $arr["mode"]?>"><?php echo formatMode($arr["mode"])?></a></td>
<td><a href="bandMode.php?satellitename=<?php echo $arr["satellitename"]?>"><?php echo $arr["satellitename"] ?></a></td>
<td><a href="bandMode.php?operator=<?php echo $arr["operator"]?>"><?php echo $arr["operator"]?></a></td>
<td><a href="bandMode.php?station=<?php echo $arr["station"]?>"><?php echo $arr["station"]?></a></td>
<?php
if (isset($showEdit) && $showEdit) {
	echo ("<td><a href=\"editContact.php?id=" . $arr["id"] . "&sourceId=" . $arr["sourceId"] . "\"><img border=\"0\" src=\"edit.gif\" /></a></td>");
}
?>
</tr><?php
}
?></table></td></tr></table><?php
}

function formatDate($dateField, $delim) {
        $timestamp = strtotime($dateField);
        return date("d/m", $timestamp);
}

function formatTime($dateField) {
        $timestamp = strtotime($dateField);
        return date("H:i", $timestamp);
}

function formatMode($mode) {
	if ($mode=="JT65")
		return "JT6m";
	else
		return $mode;
}
function formatFrequency($freq) {
	$freq = round (intval($freq) / 1000);
	return number_format ($freq/1000, 3);
}

function getSquaresWorked($band) {
	global $dbName;
	$sql = "SELECT DISTINCT(LEFT(locator, 4)) loc4s FROM log WHERE locator IS NOT NULL AND locator <> 'JJ00aa' AND LENGTH(locator) > 2 AND band LIKE '" . $band . "'";
	$res = mysql_db_query($dbName, $sql) or die ("Failed getting locators worked: " . mysql_error());
	$squares = array();
	$i = 0;
	while ($arr = mysql_fetch_array($res)) {
		$squares[$i++] = $arr["loc4s"];
	}
	return $squares;
}

function countSquaresWorked($band) {
	global $dbName;
	$sql = "SELECT squarecount FROM squaresstats WHERE band='$band';";
	$res = mysql_db_query($dbName, $sql) or die ("Failed getting square count");
	if ($arr = mysql_fetch_array($res))
		return $arr["squarecount"];
	else
		return 0;
	return count(getSquaresWorked($band));
}

function updateSquaresTable() {
	echo ("Updating squares table...");
	global $dbName;
	$sql = "insert into squaresstats (band, squarecount) (select band, count(distinct(left(locator, 4))) cnt from log where locator is not null and locator <> 'JJ00aa' group by band) on duplicate key update squarecount=VALUES(squarecount)";
	mysql_db_query($dbName, $sql) or die ("Error updating squares table: " . mysql_error());
	echo ("...complete\n");	
}

function updateOperatorsTable() {
	echo ("Updating operators table...");
	global $dbName;
	$sql = "INSERT INTO operatorstats (operator, qsos) (SELECT operator, count(*) FROM log WHERE operator IS NOT NULL GROUP BY operator) ON DUPLICATE KEY UPDATE qsos=VALUES(qsos);";
	mysql_db_query($dbName, $sql) or die ("Error updating operators table: " . mysql_error());
	echo ("...complete\n");
}
