<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(60);

$survey_response_id = $_REQUEST["srid"];
$question_id = $_REQUEST["qid"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}


$r= getResult($survey_response_id,$question_id);
	echo $r;

mysql_close($db);	// Close DB connection

function checkanswer($survey_response_id,$dependent_question_id,$dependent_choice_id)
{
	$query = "SELECT `survey_answer_id` FROM `smartsurveys`.`survey_answer` WHERE `survey_response_id` = $survey_response_id and `question_id` = $dependent_question_id and `choice_id` = $dependent_choice_id";
	$result = mysql_query($query);
	if ($result)
	{
		if(mysql_num_rows($result) != 0)
		{	// Does exist.
			return 1;
		}
		else
		{
			return 0;
		}
	}
	else{
		reportDBError($query . "Query Failed.", __FILE__);
		return -2;
	}
}
function getResult($survey_response_id,$question_id)
{
	// Search if it exists.
	$query = "SELECT `dependent`,`dependent_question_id`,`dependent_choice_id` FROM `smartsurveys`.`question` WHERE `question_id` = $question_id";
	$result = mysql_query($query);

	if ($result)
	{
		while($row = mysql_fetch_assoc($result)) {
        	if($row['dependent'] == 0)
        	{
        		return 1;
        	}
        	elseif ($row['dependent'] == 1) {
        		return checkanswer($survey_response_id,$row['dependent_question_id'],$row['dependent_choice_id']);
        	}
    	}
	}
	else
	{
		reportDBError($query . "Query Failed.", __FILE__);
	}

	return -1;
}
?>