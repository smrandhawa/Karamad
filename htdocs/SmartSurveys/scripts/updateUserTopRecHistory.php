<?PHP
error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);
$ID = $_REQUEST["ID"];
$weekOrMonth= $_REQUEST["weekOrMonth"];
$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult($ID,$weekOrMonth);
echo $answer;

mysql_close($db);	// Close DB connection

function getResult($ID,$weekOrMonth)
{	
	if($weekOrMonth==1){
		$query = "UPDATE `Baang`.`usertopreclistenrecord` set week = week - 1 WHERE userid= $ID  ";
	}else{
		$query = "UPDATE `Baang`.`usertopreclistenrecord` set month = month - 1  WHERE userid= $ID  ";
	}
	$result = mysql_query($query);

	if ($result){

	}else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}		
}
?>
