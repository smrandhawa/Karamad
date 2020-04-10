<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$ID = $_REQUEST["ID"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult($ID);
echo $answer;

mysql_close($db);	// Close DB connection

function getResult($ID)
{	
	$query = "SELECT `Recording_id` FROM `Baang`.`recording_table` WHERE `Recording_id` = '$ID' and `softdelete`= 0 ";
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
