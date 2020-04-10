<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(60);

$ph = $_REQUEST["ph"];


$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}


$r= getResult($ph);
	echo $r;

mysql_close($db);	// Close DB connection

function getResult($ph)
{
	// Search if it exists.
	$query = "SELECT `worker_id` FROM `smartsurveys`.`worker` WHERE `phone_number` = '$ph'";

	$result = mysql_query($query);

	if ($result){
		if(mysql_num_rows($result) == 0){	// Does not exist. Okay, Insert.
			$query = "INSERT INTO  `smartsurveys`.`worker` (`phone_number`,`carrier`) VALUES ('$ph','');";
			$result = mysql_query($query);
			
			if ($result){
				$key = mysql_insert_id();
			}
			else{
				reportDBError($query . "Query Failed.", __FILE__);
			}
		}
		else{
			$key = mysql_result($result, 0);
		}
	}
	else{
		reportDBError($query . "Query Failed.", __FILE__);
	}
	return $key; 
}
?>