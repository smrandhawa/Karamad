<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(60);

$worker_id = $_REQUEST["wid"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}


$r= getResult($worker_id);
	echo $r;

mysql_close($db);	// Close DB connection

function getResult($worker_id)
{
	// Search if it exists.
	$query = "SELECT `carrier_entered` FROM `smartsurveys`.`worker` WHERE `worker_id` = $worker_id";
	$result = mysql_query($query);

	if ($result){
		if(mysql_num_rows($result) != 0){
			$carrier_entered = mysql_result($result, 0);
		}
		else{
		$carrier_entered = -1;
		}
	}
	else{
		$carrier_entered = -1;
		reportDBError($query . "Query Failed.", __FILE__);
	}

	return $carrier_entered;
}
?>