<?PHP
error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;
$time_zone=$GLOBALS['Global_Time_Zone'];
if(function_exists('date_default_timezone_set'))date_default_timezone_set($time_zone);

set_time_limit(15);
$recid	= $_REQUEST["recid"]; 
$callid = $_REQUEST["callid"]; 
$userid	= $_REQUEST["userid"]; 	
$vf		= $_REQUEST["vf"];
$pofr		= $_REQUEST["pofr"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

echo getResult($recid,$callid,$userid,$vf,$pofr);

mysql_close($db);	// Close DB connection

function getResult($recid,$callid,$userid,$vf,$pofr)
{
	$query = "INSERT INTO  `Baang`.`userplayrectable` (`id`,`recid` ,`userid`,`callid`,`DateTime`,`visitedFrom`,`promptedOrfinishedRecording`) 
	VALUES ('0', $recid, $userid,$callid,NOW(),$vf,$pofr);";
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