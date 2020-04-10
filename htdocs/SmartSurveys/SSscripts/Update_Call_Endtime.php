<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;
$time_zone=$GLOBALS['Global_Time_Zone'];
if(function_exists('date_default_timezone_set'))date_default_timezone_set($time_zone);

set_time_limit(15);

$callid = $_REQUEST["callid"]; 

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

echo $callid;
echo getResult($callid);

mysql_close($db);	// Close DB connection

function getResult($callid)
{
	$query = "UPDATE `smartsurveys`.`call table` SET `Call_End_Time` = NOW() WHERE `Call_ID` = $callid";
	
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