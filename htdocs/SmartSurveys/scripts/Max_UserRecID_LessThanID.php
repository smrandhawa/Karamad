<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$ID = $_REQUEST["ID"];
$userid=$_REQUEST["userid"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}


$answer = getResult($ID, $userid);
echo $answer;

mysql_close($db);	// Close DB connection

function getResult($ID, $userid)
{	
	$query="SELECT MAX(Recording_id) FROM `Baang`.`recording_table` where `Recording_id` < '$ID' and `userid` = '$userid' and `recordingexist`=1 and `softdelete`= 0 ";		
	$result = mysql_query($query);
	
	if ($result){
		$EbID = mysql_result($result, 0);
	}
	else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}	
	return $EbID;
}
?>
