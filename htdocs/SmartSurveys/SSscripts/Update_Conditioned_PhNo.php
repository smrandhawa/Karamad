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
$uncond = $_REQUEST["uncond"]; 
$cond = $_REQUEST["cond"]; 
$sender = $_REQUEST["sender"]; 
$type = $_REQUEST["type"]; 

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

echo getResult($callid, $uncond, $cond, $type, $sender);

mysql_close($db);	// Close DB connection

function getResult($callid, $uncond, $cond, $type, $sender)
{
	$query = "INSERT INTO `smartsurveys`.`phone numbers`(`ph_num-id`, `unconditioned`, `conditioned`, `date_time`, `call_id`, `call_type`, `sender`) VALUES (0, '$uncond', '$cond', NOW(), $callid, '$type', '$sender')";
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