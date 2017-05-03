<?php require_once("funcs.php.inc"); ?>
<html>
<head>
<?php if (!isset($_REQUEST["norefresh"])) { ?>
<meta http-equiv="refresh" content="10" />
<?php } ?>
<link rel="stylesheet" href="style.css" />
</head>
<body>
<? plotBandModesTable(); ?>
<h2>Last 200 stations worked (Total: <?= getTotalCount(); ?>)</h2>
<? plotCalls("ORDER BY time DESC LIMIT 200"); ?>
</body>
</html>
