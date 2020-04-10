<?PHP
error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;
$time_zone=$GLOBALS['Global_Time_Zone'];
if(function_exists('date_default_timezone_set'))date_default_timezone_set($time_zone);

set_time_limit(15);

$callid = $_REQUEST["callid"]; 
$userid	= $_REQUEST["userid"]; 	
$count	= $_REQUEST["count"]; 	 	
$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

echo getResult($callid,$userid,$count);

mysql_close($db);	// Close DB connection

function getResult($callid,$userid,$count)
{
	$query = "INSERT INTO  `Baang`.`recording_table` (`Recording_id` ,`Call_id`,`userid`,`promptedtype`) VALUES ('0',  $callid, $userid,$count);";
	$result = mysql_query($query);
	
	if ($result){
		$recID = mysql_insert_id();
	}
	else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}
	return $recID; 
}
?>