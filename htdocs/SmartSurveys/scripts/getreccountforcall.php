<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$callid = $_REQUEST["callid"];
$userid = $_REQUEST["userid"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult($userid);
echo $answer;

mysql_close($db);	// Close DB connection

function getResult($userid)
{	
	//$query = "SELECT count(*) FROM `Baang`.`recording_table` WHERE `Call_id` = '$callid' ";
	$query = "SELECT count(*) FROM `Baang`.`recording_table` WHERE `userid` = '$userid' and Date(dateofrecording)=Date(NOW()) ";
	
	$result = mysql_query($query);
	if ($result){
		if(mysql_num_rows($result)>0)
		{	$EbID = mysql_result($result, 0); 
		}
		else{
			$EbID=0;
		}
	}
	else{
		reportDBError($query . ". Query Failed.", __FILE__);
		$EbID=0;
	}	
	return $EbID;
}
?>
