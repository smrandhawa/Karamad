<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$reqid = $_REQUEST["reqid"]; 
$phno = $_REQUEST["phno"]; 
$calltype = $_REQUEST["calltype"];
$status = $_REQUEST["status"];
$channel = "";
if(isset($_REQUEST['ch'])){
	$channel = $_REQUEST['ch'];	
}

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

echo getResult($reqid, $phno, $calltype, $status, $channel);

mysql_close($db);	// Close DB connection

function getResult($reqid, $phno, $calltype, $status, $channel)
{
	$query = "INSERT INTO  `smartsurveys`.`call table` (`Call_ID`,`Originating _Request_ID`,`Phone_Number`,`Call_Type`,`Call_Start_Time`,`Call_End_Time`,`CallStatus`, `Channel`, `Attempt_Start_Time`) VALUES ('0',  '$reqid', '$phno', '$calltype', NOW(), NOW(), '$status', '$channel', NOW());";
	$result = mysql_query($query);
	if ($result){
		$callID = mysql_insert_id();
	}
	else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}
	return $callID; 
}
?>