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
	$query = "UPDATE `Baang`.`counttable` set `count`=`count`+1";
	$result = mysql_query($query);
	//echo mysql_error();
	$query = "SELECT * FROM `Baang`.`counttable`";
	$result = mysql_query($query);
	
	if ($result){
		$EbID = mysql_result($result, 0);
		//$EbID = mysql_insert_id();
		if($EbID>=4)
		{
			$query = "UPDATE `Baang`.`counttable` set count=-1";
			$result = mysql_query($query);
		}
	}
	else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}	
	return $EbID;
}
?>
