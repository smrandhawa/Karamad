<?PHP
error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$rec_id = $_REQUEST["ID"];
$userid = $_REQUEST["userid"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult($rec_id,$userid);
echo $answer;

mysql_close($db);	// Close DB connection

function getResult($id,$userid)
{	
	$query = "SELECT count(*) FROM `Baang`.`uservotesofrec` WHERE `recid` = $id and `userid` = $userid  ";
	$result = mysql_query($query);
	if ($result){
		$count = mysql_result($result, 0);
		if($count==0){
			$query = " INSERT INTO `Baang`.`uservotesofrec` (`recid`,`userid`,`type`) Values ($id,$userid,3) ";
			$result = mysql_query($query);

			$query = "UPDATE `Baang`.`recording_table` set reportvotes = reportvotes + 1 WHERE Recording_id= $id  ";
			$result = mysql_query($query);
			
			if ($result){
				$EbID = mysql_result($result, 0);
			}
			else{
				reportDBError($query . ". Query Failed.", __FILE__);
			}	
			return $EbID;
		}else{
			return -1;
		}
	}else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}					

}
?>
