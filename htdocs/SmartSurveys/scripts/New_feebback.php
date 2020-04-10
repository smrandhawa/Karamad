<?PHP
error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;
$time_zone=$GLOBALS['Global_Time_Zone'];
if(function_exists('date_default_timezone_set'))date_default_timezone_set($time_zone);

set_time_limit(15);

$RecId = $_REQUEST["RecId"]; 
$userid	= $_REQUEST["userid"]; 	
$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

echo getResult($RecId,$userid);

mysql_close($db);	// Close DB connection

function getResult($RecId,$userid)
{
//	$query="SELECT count(*) from `Baang`.`feedback_table` Where `Recording_id`=$RecId and `userid` = $userid";
//	$result = mysql_query($query);
//	if ($result){
//		$count = mysql_result($result, 0);
//		if($count==0){
			$query = "INSERT INTO  `Baang`.`feedback_table` (`srno`,`Recording_id`,`userid`) VALUES (NULL,$RecId, $userid);";
			$result = mysql_query($query);

			if ($result){
				$recID = mysql_insert_id();
			}
			else{
				reportDBError($query . ". Query Failed.", __FILE__);
				return -1;
			}
//		}else{
//			return -1;
//		}
//	}
//	else{
//		reportDBError($query . ". Query Failed.", __FILE__);
//	}
	return $recID; 
}
?>