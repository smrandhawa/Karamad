<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(60);

$worker_id = $_REQUEST["wid"];
$carrier = $_REQUEST["cir"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}


$r= getResult($worker_id,$carrier);
	echo $r;

mysql_close($db);	// Close DB connection

function getResult($worker_id,$carrier)
{
	// Search if it exists.
	$query = "SELECT `carrier_entered` FROM `smartsurveys`.`worker` WHERE `worker_id` = $worker_id";
	$result = mysql_query($query);

	if ($result){
		if(mysql_num_rows($result) != 0){	// Does not exist. Okay, Insert.
			$query="UPDATE  `smartsurveys`.`worker` SET `carrier`= '$carrier', `carrier_entered`= 1 WHERE `worker_id` = $worker_id";
					
			$result = mysql_query($query);
			
			if ($result){

				return 1;
			}
			else{
				reportDBError($query . ". Query Failed.", __FILE__);
			}
		}
		else{
			return 0;
		}
	}
	else{
		reportDBError($query . "Query Failed.", __FILE__);
	}
	return 0;
}
?>