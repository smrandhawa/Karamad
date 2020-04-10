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

$answer = getResult($ID, $cat);
echo $answer;

mysql_close($db);	// Close DB connection

function getResult($ID, $cat)
{	
	$query="SELECT MIN(Recording_id) FROM ((SELECT `Recording_id` FROM (SELECT `Recording_id`, IFNULL(	(upvotes /(downvotes + reportvotes))*(POWER(0.777, upvotes + downvotes + reportvotes)), 0) as c FROM `Baang`.`recording_table` ORDER BY `c` DESC limit 100) as a)
UNION
(SELECT `Recording_id` From (SELECT `Recording_id`, IFNULL(upvotes /(upvotes + downvotes + reportvotes) - sqrt(upvotes *(downvotes + reportvotes)/(upvotes + downvotes + reportvotes))/(upvotes + downvotes + reportvotes), 0) as c FROM `Baang`.`recording_table` ORDER BY `c` DESC limit 100) as b)) as z where `Recording_id` > '$ID'";
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
