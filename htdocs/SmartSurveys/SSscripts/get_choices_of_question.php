<?php

header('Content-Type: application/json');

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$question_id = $_REQUEST["qid"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult($question_id);

echo json_encode($answer);

mysql_close($db);	// Close DB connection

function getResult($question_id)
{
	$query = "SELECT * from `smartsurveys`.`choice` where question_id=$question_id  ORDER BY `choice`.`choice_order` asc";


	$result = mysql_query($query);
	//echo mysql_error();
	//echo $query;
	$rows = array();
	if ($result){
			while($row = mysql_fetch_assoc($result)) {
        			//array_push($IDarray, $array($row["srno"],$row["userid"],$row["Recording_id"]));
					$rows[] = $row;
    			}
	}
	else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}
	return $rows;
}

?>