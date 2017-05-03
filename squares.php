<?php 
	require_once("funcs.php.inc"); 
	$band = $_REQUEST["band"];
?>
<html>
<head>
<link rel="stylesheet" href="style.css" />
<meta http-equiv="refresh" content="10" />
</head>
<body>
<table align="center" class="bandModesTableOutline"><tr><td><table class="bandModesTable"><tr class="bandModesTableHead">
<td>Squares on <?php echo $band ?></td>
</tr>
<?php
foreach (getSquaresWorked($band) as $square) {
	echo("<tr><td>$square</td></tr>");
}
?>
</table>
</body>
</html>
