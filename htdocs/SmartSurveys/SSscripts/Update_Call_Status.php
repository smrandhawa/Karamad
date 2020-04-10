<?PHP

// Close connection early
// early close reference: http://www.zulius.com/how-to/close-browser-connection-continue-execution/
// buffer all upcoming output
ob_start();
echo "Early Return.";
 
// get the size of the output
$size = ob_get_length();
 
// send headers to tell the browser to close the connection
header("Content-Length: $size");
header('Connection: close');
 
// flush all output
ob_end_flush();
ob_flush();
flush();
 
// close current session
if (session_id()) session_write_close();
// Connection has been closed now and here starts the background process

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;
$time_zone=$GLOBALS['Global_Time_Zone'];
if(function_exists('date_default_timezone_set'))date_default_timezone_set($time_zone);
set_time_limit(15);

$callid = $_REQUEST["callid"]; 
$status = $_REQUEST["status"]; 

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

echo getResult($callid, $status);

mysql_close($db);	// Close DB connection

function getResult($callid, $status)
{
	if($status == 'InProgress'){
		$query = "UPDATE `smartsurveys`.`call table` SET `CallStatus` = '$status', `Call_Start_Time` = NOW() WHERE `Call_ID` = $callid";
	}
	else{
		$query = "UPDATE `smartsurveys`.`call table` SET `CallStatus` = '$status' WHERE `Call_ID` = $callid";
	}
	
	$result = mysql_query($query);
	if ($result){
		$callID = mysql_insert_id();
	}
	else{
		reportDBError($query . "Query Failed.", __FILE__);
	}
	return $callID; 
}
?>