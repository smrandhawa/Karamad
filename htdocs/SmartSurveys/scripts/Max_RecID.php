<?PHP
include "..\\wa\\config.php";

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$type = $_REQUEST["type"];
$userid = $_REQUEST["userid"];
$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult($userid,$type);
echo serialize($answer);

mysql_close($db);	// Close DB connection

function getResult($userid,$type)
{

//	"SELECT Recording_id,(upvotes/(downvotes+reportvotes))*(POWER(0.777,upvotes+downvotes+reportvotes)) as c FROM `recording_table` ORDER BY `c` DESC limit 100";
	if($type==1){
		#$query = "SELECT `Recording_id` From ( SELECT `Recording_id`, IFNULL(	(upvotes /(downvotes + reportvotes))*(POWER(0.777, totalvotes)), 0) as c FROM `Baang`.`recording_table` ORDER BY `c`  DESC , Recording_id DESC limit 100) as b";   // latest + popular
		$query="SELECT `Recording_id`,`upvotes`,`userid` FROM `Baang`.`recording_table` where `userid` != '$userid' and `Recording_id`>'".$GLOBALS['baangRecordingIDcutoff']."' and `recordingexist` = 1 and `softdelete`= 0 ORDER BY `recording_table`.`dateofrecording`  DESC LIMIT 100"; // latest
		//echo $query;
	}elseif ($type==2) {
//		$query="SELECT `Recording_id` From (SELECT `Recording_id`, IFNULL(upvotes /(totalvotes) - sqrt(upvotes *(downvotes + reportvotes)/(totalvotes))/(totalvotes), 0) as c FROM `Baang`.`recording_table` where `userid` != '$userid' and `recordingexist` = 1 ORDER BY `c` DESC, Recording_id Desc  limit 100) as b";  // popular
		$query ="SELECT `Recording_id`,`upvotes`,`userid` FROM `Baang`.`recording_table` where `dateofrecording`>= NOW() - INTERVAL 7 DAY and `recordingexist` = 1 and `softdelete`= 0 order by `upvotes` desc, `downvotes` asc";
		# code...
	}else{
		$query="SELECT `Recording_id`,`upvotes`,`userid` FROM `Baang`.`recording_table` where `dateofrecording`>= NOW() - INTERVAL 1 DAY and `recordingexist` = 1 and `softdelete`= 0 order by `upvotes` desc, `downvotes` asc";
	}

	$result = mysql_query($query);
	echo mysql_error();
	//echo $query;
	$IDarray = array(
		// array(
		// 	"Recording_id"		=> "33949",
		// 	"upvotes"			=> "314",
		// 	"userid"			=> "3566" 
		// )
	);
	if ($result){
			while($row = mysql_fetch_assoc($result)) {
        			//array_push($IDarray, $row["Recording_id"]);
					$IDarray[] = $row;
    			}
	}
	else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}	
	return $IDarray;
}
?>
