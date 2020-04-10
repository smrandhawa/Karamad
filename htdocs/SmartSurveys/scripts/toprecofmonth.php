<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult();
echo $answer;

mysql_close($db);	// Close DB connection

function getResult()
{	
	$query = "SELECT `rec_id` FROM `Baang`.`toprecofmonth` limit 1";
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
