<?php

header('Content-Type: application/json');

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$survey_id = $_REQUEST["sid"];
$user_id = $_REQUEST["uid"];

//echo $survey_id;
//echo $user_id;

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

echo getResult($survey_id,$user_id);

mysql_close($db);	// Close DB connection

function getResult($survey_id,$user_id)
{

	$query="SELECT `survey_response_id` from `smartsurveys`.`survey_response` where survey_id = $survey_id and worker_id = $user_id";
	//echo $query;
	$result=mysql_query($query);
	if($result){
		if(mysql_num_rows($result) == 0){	// Does not exist. Okay, Insert.

			$query="INSERT INTO `smartsurveys`.`survey_response`(`survey_id`, `worker_id`, `pay_status`, `survey_completed`) VALUES ($survey_id,$user_id,0,0)";
			//echo $query;
			$result = mysql_query($query);
			if ($result){
				$survey_response_id = mysql_insert_id();
			}
			else{
				reportDBError($query . ". Query Failed.", __FILE__);
			}

		}else{
			$survey_response_id = mysql_result($result, 0);
		}
	}else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}
	return $survey_response_id; 
}

?>