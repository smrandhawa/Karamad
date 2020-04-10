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

$reqid = $_REQUEST["reqid"]; 
$status = $_REQUEST["status"]; 
$ch = '';
if(isset($_REQUEST["ch"])){
	$ch = $_REQUEST["ch"];
}

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

echo getResult($reqid, $status, $ch);

mysql_close($db);	// Close DB connection

function getResult($reqid, $status, $ch)
{
	$updateNoOfAttempts = ",`Number_of_Attempts`=`Number_of_Attempts`+1";	// default
	$updateSystemRetries = ",`No_of_System_Attempts_for_this_Attempt`=0";	// default
/*	if($ch != 'Cloud' && $status == 'unfulfilled'){
		$updateSystemRetries = ",`No_of_System_Attempts_for_this_Attempt`=`No_of_System_Attempts_for_this_Attempt`+1";
		$updateNoOfAttempts = "";
	}
*/	
	$query = "UPDATE `smartsurveys`.`request table` SET `Request_Status`='$status',`Time_of_Last_Attempt`=NOW() $updateSystemRetries $updateNoOfAttempts WHERE `Request_ID` = $reqid";

	$result = mysql_query($query);
	
	if ($result){
		$reqid = mysql_insert_id();
	}
	else{
		reportDBError($query . "Query Failed.", __FILE__);
	}
	return $reqid; 
}
?>