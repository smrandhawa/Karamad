<?PHP
error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$srno = $_REQUEST["srno"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult($srno);
echo $answer;

mysql_close($db);	// Close DB connection

function getResult($srno)
{	
		$query = "UPDATE `Baang`.`feedback_table` set softdelete=1 WHERE srno= $srno  ";
		$result = mysql_query($query);
		
		if ($result){
			$EbID = mysql_result($result, 0);
		}
		else{
			reportDBError($query . ". Query Failed.", __FILE__);
		}	
}
?>
