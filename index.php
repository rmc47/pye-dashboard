<?php require_once("funcs.php.inc"); 
$_SESSION["showEdit"] = true;
?>
<html>
<head>
<link rel="stylesheet" href="style.css" />
<meta http-equiv="refresh" content="10" />
</head>
<body>
<!--<h2>Last worked G0TAR: <?php echo getLastWorked("G0TAR"); ?></h2>-->
<span style="float: left;"><?php plotBandModesTable(); ?></span>
<span style="float: left;"><?php plotOperatorsTable(); ?></span>
<span style="float: left;"><?php plotLatestFrequenciesTable(); ?></span>
<div style="clear:both;"></div>
<h2>Search log</h2>
<form method="get" action="bandMode.php">
	<input type="text" name="call" />&nbsp;<input type="submit" name="submit" value = "Search" />
</form>
<h2>Last 100 stations worked (Total: <?php echo getTotalCount(); ?>)</h2>
<?php plotCalls("ORDER BY endTime DESC LIMIT 100"); ?>
</body>
</html>
