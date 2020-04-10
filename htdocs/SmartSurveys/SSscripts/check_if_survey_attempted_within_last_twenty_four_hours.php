<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(60);

$worker_id = $_REQUEST["wid"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}


$r= getResult($worker_id);
	echo $r;

mysql_close($db);	// Close DB connection

function getResult($worker_id)
{
	// Search if it exists.

	$query = "SELECT `survey_answer`.`time_attempted` FROM `smartsurveys`.`survey_answer`,`smartsurveys`.`survey_response` where  `survey_answer`.`survey_response_id` = `survey_response`.`survey_response_id` and `survey_response`.`worker_id` = $worker_id and `survey_response`.`survey_completed` = 1 ORDER BY `survey_answer`.`time_attempted` DESC limit 1";
	$result = mysql_query($query);
	//echo $query;
	$status_worked = 0;
	if ($result){
		if(mysql_num_rows($result) != 0){	// Does not exist. Okay, Insert.
			//echo "here";

			$last_worker_on_date_time = mysql_result($result, 0);
			//echo $last_worker_on_date_time;

			date_default_timezone_set('Asia/Dubai');
			$current_date_time = date('Y-m-d H:i:s');

			//echo $current_date_time;

			//echo $last_worker_on_date_time;

			$diff = abs(strtotime($current_date_time) - strtotime($last_worker_on_date_time));

			//echo "Difference between two dates: ". floor(($diff)/60/60/24);
			$time_span_in_days = floor(($diff)/60/60/24);
			if ($time_span_in_days == 0)
			{
				$status_worked = 1;
			}
		}
	}
	else{
		echo "error";
		$status_worked = -1;
		//reportDBError($query . "Query Failed.", __FILE__);
	}

	return $status_worked;
}
?>