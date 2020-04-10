<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$key = $_REQUEST["key"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult($key);
echo $answer;

mysql_close($db);	// Close DB connection

function getResult($key)
{	
	$query = "SELECT `phone_number` FROM `smartsurveys`.`worker` WHERE `worker_id` = '$key'";
	$result = mysql_query($query);
	
	if ($result){
		if(mysql_num_rows($result) == 0){
			$Phno = '0';
		}
		else{
			$Phno = mysql_result($result, 0);
		}
	}
	else{
		reportDBError($query . "Query Failed.", __FILE__);
	}
	return $Phno;
}
?>