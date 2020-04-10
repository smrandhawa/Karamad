<?php

header('Content-Type: application/json');

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$survey_response_id = $_REQUEST["srid"];
$worker_preference = $_REQUEST["pref"];

//echo $survey_id;
//echo $worker_preference;

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

echo getResult($survey_response_id,$worker_preference);

mysql_close($db);	// Close DB connection

function getResult($survey_response_id,$worker_preference)
{

	$query="SELECT `survey_feedback_id` from `smartsurveys`.`survey_feedback` where survey_response_id = $survey_response_id";
	//echo $query;
	$result=mysql_query($query);
	if($result){
		if(mysql_num_rows($result) == 0){	// Does not exist. Okay, Insert.

			$query="INSERT INTO `smartsurveys`.`survey_feedback`(`survey_response_id`, `worker_preference`, `file_name`) VALUES ($survey_response_id,'".$worker_preference."','')";
			//echo $query;
			$result = mysql_query($query);
			if ($result){
				$survey_feedback_id = mysql_insert_id();

				 if($worker_preference == "comment")
				 {
				 	$file_name = strval($survey_feedback_id) . ".wav";
				    $query="UPDATE  `smartsurveys`.`survey_feedback` SET `survey_response_id`=$survey_response_id,`worker_preference`='".$worker_preference."',`file_name`='". $file_name."' WHERE `survey_response_id` = $survey_response_id";
					
					$result = mysql_query($query);
				
				 }
			}
			else{
				reportDBError($query . ". Query Failed.", __FILE__);
			}

		}else{

			$query="UPDATE  `smartsurveys`.`survey_feedback` SET `survey_response_id`=$survey_response_id,`worker_preference`='".$worker_preference."' WHERE `survey_response_id` = $survey_response_id";
			//echo $query;
			$result = mysql_query($query);
			if ($result){

				$query="SELECT `survey_feedback_id` from `smartsurveys`.`survey_feedback` where survey_response_id = $survey_response_id";
				$result=mysql_query($query);
				$survey_feedback_id = mysql_result($result, 0);

				if($worker_preference == "comment")
				 {	
				 	$file_name = strval($survey_feedback_id) . '.wav';
				 
				 	
				    $query="UPDATE  `smartsurveys`.`survey_feedback` SET `survey_response_id`=$survey_response_id,`worker_preference`='".$worker_preference."',`file_name`='". $file_name."' WHERE `survey_response_id` = $survey_response_id";
					//echo $query;
					$result = mysql_query($query);
				
				 }

				//echo $survey_answer_id;
			}
			else{
				reportDBError($query . ". Query Failed.", __FILE__);
			}
		}
	}else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}
	return $survey_feedback_id; 
}

?>