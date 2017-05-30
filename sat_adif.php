<?php
	header("content-type: plain/text"); 
	header('Content-Disposition: attachment; filename="satlog.adi"')
?>
<ADIF_VERS:3>2.2
<PROGRAMID:<?php echo strlen("PYEADIF"); ?>><?php echo "PYEADIF"."\n"; ?>
<PROGRAMVERSION:<?php echo strlen("1"); ?>>Version <?php echo "1"."\n"; ?>
<EOH>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lewis";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT * FROM log WHERE station = \"SATS\"";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
?>

<?php while($row = $result->fetch_assoc()) {  ?>
<call:<?php echo strlen($row["callsign"]); ?>><?php echo $row["callsign"]; ?><?php $date_on = strtotime($row["startTime"]); $new_date = date('Ymd', $date_on); ?><qso_date:<?php echo strlen($new_date); ?>><?php echo $new_date; ?><?php $time_on = strtotime($row["startTime"]); $new_on = date('His', $time_on); ?><time_on:<?php echo strlen($new_on); ?>><?php echo $new_on; ?><?php $time_off = strtotime($row["endTime"]); $new_off = date('His', $time_off); ?><time_off:<?php echo strlen($new_off); ?>><?php echo $new_off; ?><rst_rcvd:<?php echo strlen($row["reportRx"]); ?>><?php echo$row["reportRx"]; ?><rst_sent:<?php echo strlen($row["reportTx"]); ?>><?php echo $row["reportTx"]; ?><sat_mode:<?php echo strlen($row["satellitemode"]); ?>><?php echo $row["satellitemode"]; ?><sat_name:<?php echo strlen($row["satellitename"]); ?>><?php echo $row["satellitename"]; ?><band:<?php echo strlen($row["band"]); ?>><?php echo $row["band"]; ?><mode:<?php echo strlen($row["mode"]); ?>><?php echo $row["mode"]; ?> <prop_mode:3>SAT<eor>
<?php }
} else {
    echo "0 results";
}
$conn->close();
?>