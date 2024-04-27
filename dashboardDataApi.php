<?php require_once("funcs.php.inc"); 
$obj = array();
$obj["frequencies"] = array();
$obj["topOps"] = array();
$ops = getOperators();
foreach ($ops as $op) {
	$obj["topOps"]["$op"] = getOperatorCount($op);
}
$obj["totals"] = array();
$obj["totals"]["totalQsos_all"] = getTotalCount();
$obj["totals"]["distinct_all"] = getDistinctCount();
$obj["totals"]["totalQsos_vhf"] = 0;
$obj["frequencies"] = getLatestFrequenciesArray();
$obj["modes"] = array();
foreach (getAllModes() as $mode) {
	$obj["modes"][$mode] = 100 * getModeCount($mode) / getTotalCount();
}
$obj["lastLocators"] = getLastLocatorsArray();
$obj["meta"] = array();
$obj["meta"]["created"] = date(DATE_RFC850);
echo json_encode($obj);
?>
