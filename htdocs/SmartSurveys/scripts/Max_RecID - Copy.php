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
echo serialize($answer);

mysql_close($db);	// Close DB connection

function getResult()
{

//	"SELECT Recording_id,(upvotes/(downvotes+reportvotes))*(POWER(0.777,upvotes+downvotes+reportvotes)) as c FROM `recording_table` ORDER BY `c` DESC limit 100";

	$query = "	SELECT `Recording_id` FROM (SELECT `Recording_id`, IFNULL(	(upvotes /(downvotes + reportvotes))*(POWER(0.777, totalvotes)), 0) as c FROM `Baang`.`recording_table` ORDER BY `c` DESC limit 100) as a
												UNION
				SELECT `Recording_id` From (SELECT `Recording_id`, IFNULL(upvotes /(totalvotes) - sqrt(upvotes *(downvotes + reportvotes)/(totalvotes))/(totalvotes), 0) as c FROM `Baang`.`recording_table` ORDER BY `c` DESC limit 100) as b";
	$result = mysql_query($query);
	echo mysql_error();
	//echo $query;
	$IDarray = array();
	if ($result){
			while($row = mysql_fetch_assoc($result)) {
        			array_push($IDarray, $row["Recording_id"]);
    			}
	}
	else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}	
	return $IDarray;
}
?>
