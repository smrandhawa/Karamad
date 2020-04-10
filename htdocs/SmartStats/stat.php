


<?php

$servername = "58.27.219.91";
$username = "polly";
$password = 'GsRaTwsAg1';
$dbname = "smartsurveys";
$port = "202";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port );
// Check connection
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
} 


function executeit($sql)
{
	global $conn;

	$result = $conn->query($sql);

	if(!empty($result))
	{
		if ($result->num_rows > 0) 
		{
		     // output data of each row
			$row = $result->fetch_assoc();
			//echo $row['result']."-";
		    return $row['result'];
		} 
		else 
		{
		     //echo "0-";
			return 0;     
		}
	} 
	else 
	{
		return 0;
	}
}


$sql1 = "SELECT COUNT(*) as result FROM `call table`";
$result1 = executeit($sql1);

$sql2 = "SELECT COUNT(*) as result FROM `call table` where `CallStatus` = 'PreAnswer'";
$result2 = executeit($sql2);

$sql3 = "SELECT COUNT(*) as result FROM `call table` where `CallStatus` = 'Answered'";
$result3 = executeit($sql3);

$sql4 = "SELECT count(DISTINCT(`Phone_Number`)) as result FROM `call table`";
$result4 = executeit($sql4);

$sql5 = "SELECT count(distinct(`worker_id`)) as result FROM `survey_response`";
$result5 = executeit($sql5);

$sql6 = "SELECT count(distinct(`survey_response_id`)) as result FROM `survey_response`";
$result6 = executeit($sql6);

$sql7 = "SELECT count(distinct(`survey_response_id`)) as result FROM `survey_response` where `survey_completed` = 1";
$result7 = executeit($sql7);

$sql8 = "SELECT count(distinct(`survey_answer_id`)) as result FROM `survey_answer`";
$result8 = executeit($sql8);

$sql9 = "SELECT count(distinct(`survey_answer_id`)) as result FROM `survey_answer` where `survey_answer`.`survey_response_id` in (SELECT `survey_response_id` FROM `survey_response` where `pay_status` = 1)";
$result9 = executeit($sql9);



function Number_of_Calls()
{

	global $result1;
	return $result1;
}

function Number_of_PreAnswer_Calls()
{

	global $result2;
	return $result2;
}

function Number_of_Answered_Calls()
{

	global $result3;
	return $result3;
}

function Number_of_Users()
{

	global $result4;
	return $result4;
}

function Number_of_Users_who_Attempted_Survey()
{

	global $result5;
	return $result5;
}

function Number_of_Surveys_Attempted()
{

	global $result6;
	return $result6;
}

function Number_of_Surveys_Completed()
{

	global $result7;
	return $result7;
}

function Number_of_Questions_Answered()
{

	global $result8;
	return $result8;
}

function Total_Money_Paid()
{

	global $result9;
	return ((($result9 * 10)*100)/88);
}

//echo number_format(Number_of_Calls());
//echo "\n";
//echo number_format(Number_of_PreAnswer_Calls());
//echo "\n";
//echo number_format(Number_of_Answered_Calls());
//echo "\n";
//echo number_format(Number_of_Users());
//echo "\n";
//echo number_format(Number_of_Users_who_Attempted_Survey());
//echo "\n";
//echo number_format(Number_of_Surveys_Attempted());
//echo "\n";
//echo number_format(Number_of_Surveys_Completed());
//echo "\n";
//echo number_format(Number_of_Questions_Answered());
//echo "\n";
//echo number_format(Total_Money_Paid());
//echo "\n";
#Money Paid: approximately: Rs. 38750 ($ 250)
#Time Period: 30 November 2019 - 6 January 2020 (37 Days)

$conn->close();

?> 