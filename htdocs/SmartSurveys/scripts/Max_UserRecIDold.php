<?PHP
error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$userid=$_REQUEST["userid"];
$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult($userid,$type);
echo serialize($answer);

mysql_close($db);	// Close DB connection

function getResult($userid)
{	
	$query = "SELECT * FROM `Baang`.`recording_table` where `userid` = $userid and `softdelete`= 0 and `recordingexist`=1 ORDER BY `Recording_id` DESC  ";
	
	$result = mysql_query($query);
	//	echo mysql_error();
	//echo $query;
	
	$IDarray = array( 
		// array(
		// 	"Recording_id"		=> "33949",
		// 	"upvotes"			=> "498",
		// 	"Call_id"			=> "0",
		// 	"userid"			=> "3566",
		// 	"downvotes"			=> "0",
		// 	"reportvotes"		=> "0",
		// 	"totalvotes"		=> "489",
		// 	"recordingexist"	=> "1",
		// 	"dateofrecording"	=> "2017-04-10 21:00:00",
		// 	"urdutranscription"	=> "0",
		// 	"englishtranscription"	=> "0",
		// 	"promptedtype"		=> "7",
		// 	"softdelete"		=> "0"
		// );
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
