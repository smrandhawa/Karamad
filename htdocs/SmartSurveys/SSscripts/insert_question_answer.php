<?php

header('Content-Type: application/json');

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$survey_response_id = $_REQUEST["srid"];
$question_id = $_REQUEST["qid"];
$choice_id = $_REQUEST["cid"];

//echo $survey_id;
//echo $user_id;

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

echo getResult($survey_response_id,$question_id,$choice_id);

mysql_close($db);	// Close DB connection

function getResult($survey_response_id,$question_id,$choice_id)
{

	$query="SELECT `survey_answer_id` from `smartsurveys`.`survey_answer` where survey_response_id = $survey_response_id and question_id = $question_id";
	//echo $query;
	$result=mysql_query($query);
	if($result){
		if(mysql_num_rows($result) == 0){	// Does not exist. Okay, Insert.

			$query="INSERT INTO `smartsurveys`.`survey_answer`(`survey_response_id`, `question_id`, `choice_id`, `file_name`, `time_attempted`) VALUES ($survey_response_id,$question_id,$choice_id,'', NOW())";
			//echo $query;
			$result = mysql_query($query);
			if ($result){
				$survey_answer_id = mysql_insert_id();

				 if($choice_id == "0")
				 {
				 	$file_name = strval($survey_answer_id) . ".wav";
				    $query="UPDATE  `smartsurveys`.`survey_answer` SET `survey_response_id`=$survey_response_id,`question_id`=$question_id,`choice_id`=$choice_id,`file_name`='". $file_name."',`time_attempted`= NOW() WHERE `survey_response_id` = $survey_response_id and `question_id` = $question_id";
					
					$result = mysql_query($query);
				
				 }
			}
			else{
				reportDBError($query . ". Query Failed.", __FILE__);
			}

		}else{

			$query="UPDATE  `smartsurveys`.`survey_answer` SET `survey_response_id`=$survey_response_id,`question_id`=$question_id,`choice_id`=$choice_id,`time_attempted`= NOW() WHERE `survey_response_id` = $survey_response_id and `question_id` = $question_id";
			//echo $query;
			$result = mysql_query($query);
			if ($result){

				$query="SELECT `survey_answer_id` from `smartsurveys`.`survey_answer` where survey_response_id = $survey_response_id and question_id = $question_id";
				$result=mysql_query($query);
				$survey_answer_id = mysql_result($result, 0);

				if($choice_id == "0")
				 {	
				 	$file_name = strval($survey_answer_id) . '.wav';
				 
				 	
				    $query="UPDATE  `smartsurveys`.`survey_answer` SET `survey_response_id`=$survey_response_id,`question_id`=$question_id,`choice_id`=$choice_id,`file_name`='". $file_name."',`time_attempted`= NOW() WHERE `survey_response_id` = $survey_response_id and `question_id` = $question_id";
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
	return $survey_answer_id; 
}

?>