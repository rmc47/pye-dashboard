<?php require_once("funcs.php.inc"); 
$sourceId = intval($_REQUEST["sourceId"]);
$id = intval($_REQUEST["id"]);
$arr = $db->query("SELECT * FROM log WHERE sourceId=$sourceId AND id=$id")->fetch_assoc() or die ($db->error);

if (isset($_REQUEST["submit"])) {
	// Going to process an edit
	$startTime=mysql_escape_string($_REQUEST["time"]);
	$callsign=mysql_escape_string($_REQUEST["callsign"]);
	$frequency=intval($_REQUEST["frequency"]);
	$mode=mysql_escape_string($_REQUEST["mode"]);
	$operator=mysql_escape_string($_REQUEST["operator"]);
	$station=mysql_escape_string($_REQUEST["station"]);
	$lastModified = date("Y-m-d H:i:s");
	$sql = "UPDATE log SET startTime='$startTime', callsign='$callsign', frequency=$frequency, mode='$mode', operator='$operator', station='$station', lastModified='$lastModified' WHERE id=$id AND sourceId=$sourceId;";
	$db->query($sql) or die ("Update failed: " . $db->error);
	echo ("Update processed");
	header("Location: index.php");
}
else
{
	?>
	<html>
	<head>
	<link rel="stylesheet" href="style.css" />
	</head>
	<body>
	<h2>Edit contact</h2>
	<form method="POST">
	<input type="hidden" name="id" value="<?php echo $id ?>" />
	<input type="hidden" name="sourceId" value="<?php echo $sourceId ?>" />
	<table class="logTableOutline" style="width: auto;"><tr><td>
	<table class="logTable">
	<tr><td>Time:</td><td><?php echo $arr["startTime"] ?></td><td><input type="text" name="time" value="<?php echo $arr["startTime"] ?>" /></td>
	<tr><td>Callsign:</td><td><?php echo $arr["callsign"] ?></td><td><input type="text" name="callsign" value="<?php echo $arr["callsign"] ?>" /></td>
	<tr><td>Frequency:</td><td><?php echo $arr["frequency"] ?></td><td><input type="frequency" name="frequency" value="<?php echo $arr["frequency"] ?>" /></td>
	<tr><td>Mode:</td><td><?php echo $arr["mode"] ?></td><td><input type="text" name="mode" value="<?php echo $arr["mode"] ?>" /></td>
	<tr><td>Worked by:</td><td><?php echo $arr["operator"] ?></td><td><input type="text" name="operator" value="<?php echo $arr["operator"] ?>" /></td>
	<tr><td>Station:</td><td><?php echo $arr["station"] ?></td><td><input type="text" name="station" value="<?php echo $arr["station"] ?>" /></td>
	<tr><td colspan="3"><input type="submit" name="submit" value="Save Changes" /> <input type="reset" name="reset" value="Reset" /></td></tr>
	</table>
	</td></tr></table>
	</form>
	</body>
	</html>
	<?php

}
?>
