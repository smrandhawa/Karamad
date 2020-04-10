<?php

header('Content-Type: application/json');

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$worker_id = $_REQUEST["wid"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult($worker_id);

echo json_encode($answer);

mysql_close($db);	// Close DB connection

function getResult($worker_id)
{
	$query = "SELECT * from `smartsurveys`.`survey` where `survey`.`survey_id` in (SELECT `survey_response`.`survey_id` FROM `smartsurveys`.`survey_response` where `survey_response`.`worker_id` = $worker_id and `survey_response`.`survey_completed` = 1 and `survey_response`.`pay_status` = 0) ORDER BY `survey`.`time_modified` DESC";
	
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