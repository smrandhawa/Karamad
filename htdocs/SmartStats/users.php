<?php


//echo "Update Script started. <br>";

$servername = "58.27.219.91:202";
$username = "polly";
$password = 'GsRaTwsAg1';
$dbname = "smartsurveys";


$con = mysql_connect($servername, $username, $password);

if (!$con) {
  die('Could not connect: ' . mysql_error());
}

mysql_select_db($dbname, $con);


$result = mysql_query("SELECT MAX(DATE(`Call_Start_Time`)) FROM `call table`");	// What is the current date?
$maxDate = mysql_result($result, 0);



$tempDate = '0000-00-00';

$result = mysql_query("SELECT MIN(DATE(`Call_Start_Time`)) FROM `call table`");
$minDate = mysql_result($result, 0);

$tempDate = $minDate;

$DateToStart = $tempDate;	// Just holding this value for later loops

$iter = 1;
$last = 0;
$row = '';

#$tempDate <= $maxDate 

$filename = 'file.txt';
$data = array();
array_push($data, $maxDate."\n");


if (file_exists($filename)) {

	$file = fopen($filename,"r");
	$lastmaxdate = fgets($file);
	
	$date = strtotime("+0 day", strtotime($lastmaxdate));
	$lastmaxdate = date("Y-m-d", $date);
	//print_r($lastmaxdate);
	while($tempDate < $lastmaxdate)
	{

		$row = fgets($file);
		//echo $row;
		array_push($data, $row);
		$date = strtotime("+1 day", strtotime($tempDate));
		$tempDate = date("Y-m-d", $date);
		$iter++;
	}
    
}
else{
	echo "file not found";
} 



//print_r($tempDate); 

//print_r($maxDate); 


$rows = '';
while($tempDate <= $maxDate){	// Now update all the remaining days, if any
	

 	
 	$cc = 0;

	$result = mysql_query("SELECT COUNT(DISTINCT(`Phone_Number`)) as count FROM `call table` where DATE(`Call_Start_Time`) = '$tempDate'");
	$row = mysql_fetch_array($result);

	$result2 = mysql_query("SELECT COUNT(DISTINCT(`Phone_Number`)) as count FROM `call table` where DATE(`Call_Start_Time`) <= '$tempDate'");
	$result3 = mysql_query("SELECT COUNT(DISTINCT(`Phone_Number`)) as count FROM `call table` where DATE(`Call_Start_Time`) < '$tempDate'");
	$row2 = mysql_fetch_array($result2);
	$row3 = mysql_fetch_array($result3);

	$result4 = mysql_query("SELECT COUNT(`Call_ID`) as count FROM `call table` where DATE(`Call_Start_Time`) = '$tempDate'");
	$row4 = mysql_fetch_array($result4);

	if(!empty($row)){
	
		$count = $row["count"];
		

		//echo $tempDate."\t".$count."\t";
		$rows = $rows.$tempDate."\t".$count."\t";


	} else {

		//echo $tempDate."0\t";
		$rows = $rows.$tempDate."0\t";
		$cc = 0;
	}

	if(!empty($row3)){
	
		
		$newUsers = (int)$row2["count"] - (int)$row3["count"];
		//echo $newUsers."\t";
		$rows = $rows.$newUsers."\t";

	} else {

		//echo "0\t";
		$rows = $rows."0\t";
	}
	
	if(!empty($row4)){
	
		$count = $row4["count"];
		
		//echo $count."\t";
		$rows = $rows.$count."\t";


	} else {

		//echo "0\t";
		$rows = $rows."0\t";
	}

	$date = strtotime("+1 day", strtotime($tempDate));
	$tempDate = date("Y-m-d", $date);
	$iter++;

	// Cumulative Users
	if($iter!=1){
			//echo "<br>";
			$rows = $rows."<br>";
			array_push($data, $rows."\n");
			$rows = '';

 	}

}

$cou = count($data);

$i = 0;
$fp = fopen('file.txt', 'w');
while($i < $cou)
{
	fwrite($fp, $data[$i]);
	$i++;
}

fclose($fp);


mysql_close($con);
?> 