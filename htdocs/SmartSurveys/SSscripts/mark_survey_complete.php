<?php

header('Content-Type: application/json');

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$survey_response_id = $_REQUEST["srid"];

//echo $survey_id;
//echo $user_id;

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

echo getResult($survey_response_id);

mysql_close($db);	// Close DB connection

function getResult($survey_response_id)
{

	$query="SELECT `survey_response_id` from `smartsurveys`.`survey_response` where survey_response_id = $survey_response_id";
	
	$result=mysql_query($query);

	if($result){
		if(mysql_num_rows($result) != 0){	// Does not exist. Okay, Insert.

			$query="UPDATE  `smartsurveys`.`survey_response` SET `survey_completed`= 1 WHERE `survey_response_id` = $survey_response_id";
					
			$result = mysql_query($query);
			
			if ($result){

				return 1;
			}
			else{
				reportDBError($query . ". Query Failed.", __FILE__);
			}
		}
	}else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}

	return 0; 
}

?>