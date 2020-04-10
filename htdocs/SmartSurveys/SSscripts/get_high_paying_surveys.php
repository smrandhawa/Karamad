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

function queryandReturnColumnasArrayResult($query,$column)
{
	$tmp_result = mysql_query($query);
	$tmp_rows = array();

	if ($tmp_result)
	{
		while($tmp_row = mysql_fetch_assoc($tmp_result)) 
		{		
    		$tmp_rows[] = $tmp_row[$column];
		}
	}
	else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}
	return $tmp_rows;
}

function checkifsatisfyquestionconditions($worker_id,$question_conditions_of_potential_survey)
{
	foreach ($question_conditions_of_potential_survey as $question_condition) {
		$question_condition_survey_id = $question_condition['question_condition_survey_id'];
		$query_survey_response_id = "SELECT `survey_response`.`survey_response_id` FROM `smartsurveys`.`survey_response` where `survey_response`.`worker_id` = $worker_id and `survey_response`.`survey_id` = $question_condition_survey_id";
		$survey_response_id = queryandReturnColumnasArrayResult($query_survey_response_id,'survey_response_id');

		if($survey_response_id)
		{
			$question_id = $question_condition['question_id'];
			$choice_id = $question_condition['choice_id'];
			$query_response_question_choice_pair = "SELECT `survey_answer`.`survey_answer_id` FROM `smartsurveys`.`survey_answer` where `survey_answer`.`survey_response_id` = $survey_response_id[0] and `survey_answer`.`question_id` = $question_id and `survey_answer`.`choice_id` = $choice_id";
			$survey_answer_id = queryandReturnColumnasArrayResult($query_response_question_choice_pair,'survey_answer_id');
			if (empty($survey_answer_id))
			{
				return false;
			}

		}
		else
		{
			return false;
		}
	}

	return true;
}

function checkifresponsesleft($potential_survey_id_with_responses_left)
{
	//echo $potential_survey_id_with_responses_left;

	$query_survey_responses = "SELECT count(`survey_response`.`survey_response_id`) FROM `smartsurveys`.`survey_response` where `survey_response`.`survey_id` = $potential_survey_id_with_responses_left and `survey_response`.`survey_completed` = 1";
	//echo $query_survey_responses;

	$query_survey_responses_result = mysql_query($query_survey_responses);
	$query_survey_responses_row = mysql_fetch_array($query_survey_responses_result);
	$survey_responses_count = $query_survey_responses_row[0];

	//echo $survey_responses_count;

	$query_total_possible_respondents = "SELECT `survey`.`no_of_respondents` from `smartsurveys`.`survey` where `survey`.`survey_id` = $potential_survey_id_with_responses_left";
	$query_total_possible_respondents_result = mysql_query($query_total_possible_respondents);
	$query_total_possible_respondents_row = mysql_fetch_array($query_total_possible_respondents_result);
	$survey_total_no_of_respondents = $query_total_possible_respondents_row[0];

	if ($survey_responses_count < $survey_total_no_of_respondents)
	{
		return true;
	}
	else
	{
		return false;
	}
}


function getResult($worker_id)
{
	$query_for_surveys_done = "SELECT `survey_response`.`survey_id` FROM `smartsurveys`.`survey_response` where `survey_response`.`worker_id` = $worker_id and `survey_response`.`survey_completed` = 1";
	$query_for_surveys_not_done = "SELECT `survey`.`survey_id` from `smartsurveys`.`survey` where `survey`.`survey_id` not in (SELECT `survey_response`.`survey_id` FROM `smartsurveys`.`survey_response` where `survey_response`.`worker_id` = $worker_id and `survey_response`.`survey_completed` = 1)";
	$surveysdone = queryandReturnColumnasArrayResult($query_for_surveys_done,'survey_id');
	$surveysnotdone = queryandReturnColumnasArrayResult($query_for_surveys_not_done,'survey_id');

	$availables_surveys_ids = array();
	foreach ($surveysnotdone as $potential_available_survey) 
	{
		$query_for_condition_surveys = "SELECT `survey_condition`.`complete_condition_survey_id` from `smartsurveys`.`survey_condition` where `survey_condition`.`survey_id` = $potential_available_survey";
		$condition_surveys_of_potential_survey = queryandReturnColumnasArrayResult($query_for_condition_surveys,'complete_condition_survey_id');

		if(!empty($condition_surveys_of_potential_survey))
		{
			$all_condition_surveys_completed = true;
			foreach ($condition_surveys_of_potential_survey as $condition_survey) 
			{
				if (!in_array($condition_survey, $surveysdone)) 
				{
				    $all_condition_surveys_completed = false;
				    break;
				}
			}
			if($all_condition_surveys_completed)
			{
				$availables_surveys_ids[] = $potential_available_survey;
			}
		}
		else
		{
			$availables_surveys_ids[] = $potential_available_survey;
		}
	}

	// Filter availables_surveys_ids based on Question Conditions
	$availables_surveys_ids_filtered_by_question_conditions = array();
	
	foreach ($availables_surveys_ids as $potential_available_survey)
	{
		$query_for_question_conditions = "SELECT `question_condition`.`question_condition_survey_id`,`question_condition`.`question_id`,`question_condition`.`choice_id` from `smartsurveys`.`question_condition` where `question_condition`.`survey_id` = $potential_available_survey";
		$question_conditions_of_potential_survey_results = mysql_query($query_for_question_conditions);
		$question_conditions_of_potential_survey = array();
		if ($question_conditions_of_potential_survey_results)
		{
			while($question_condition_of_potential_survey_row = mysql_fetch_assoc($question_conditions_of_potential_survey_results)) 
			{
				$question_conditions_of_potential_survey[] = $question_condition_of_potential_survey_row;
	    	}
		}
		else
		{
			reportDBError($query . ". Query Failed.", __FILE__);
		}

		if(!empty($question_conditions_of_potential_survey))
		{
			if(checkifsatisfyquestionconditions($worker_id,$question_conditions_of_potential_survey))
			{
				$availables_surveys_ids_filtered_by_question_conditions[] = $potential_available_survey;
			}

		}
		else
		{
			$availables_surveys_ids_filtered_by_question_conditions[] = $potential_available_survey;
		}

	}

	$surveys_ids_without_responses_filter = $availables_surveys_ids_filtered_by_question_conditions;
	// Filter availables_surveys_ids based on Question Conditions
	//echo json_encode($surveys_ids_without_responses_filter);
	$surveys_ids_filtered_by_responses = array();
	foreach ($surveys_ids_without_responses_filter as $potential_survey_id_with_responses_left)
	{
		//echo $potential_survey_id_with_responses_left;
		if(checkifresponsesleft($potential_survey_id_with_responses_left))
		{
			$surveys_ids_filtered_by_responses[] = $potential_survey_id_with_responses_left;
		}
	}
	
	$surveys_ids = join(",",$surveys_ids_filtered_by_responses);
	$rows = array();

	if($surveys_ids)
	{
		$query = "SELECT * from `smartsurveys`.`survey` where `survey`.`survey_id` IN ($surveys_ids) ORDER BY `survey`.`min_reward_per_response` DESC";
		//print_r($query);
		$result = mysql_query($query);
		if ($result)
		{
			while($row = mysql_fetch_assoc($result)) 
			{
				$rows[] = $row;
	    	}
		}
		else
		{
			reportDBError($query . ". Query Failed.", __FILE__);
		}
	}
	
	return $rows;
}

?>