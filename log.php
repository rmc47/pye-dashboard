<html>

<body>
<?
$conn = mysql_connect("localhost:3306", "logview", "log") or die(mysql_error());
$res = mysql_db_query("hrd", "SELECT * FROM log order by time desc");
?>
<table border="1">
	<tr><td>Time</td><td>Call</td><td>Operator</td></tr>
<?
while ($arr = mysql_fetch_array($res)) {
	echo ("<tr><td>" . $arr["time"] . "</td><td>" . $arr["callsign"] . "</td><td>" . $arr["COL_OPERATOR"] . "</td></tr>");
//	print_r ($arr);
//	echo ("\n\n");
}
?>
</body>
</html>
