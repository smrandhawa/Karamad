<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$ID = $_REQUEST["ID"];
$weekOrMonth= $_REQUEST["weekOrMonth"];
$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult($ID,$weekOrMonth);
echo $answer;

mysql_close($db);	// Close DB connection

function getResult($ID,$weekOrMonth)
{	
	$query = "SELECT * FROM `Baang`.`usertopreclistenrecord` WHERE `userid` = '$ID'";
	//echo $query;
	$result = mysql_query($query);
	$EbID=0;
	if ($result){
		if(mysql_num_rows($result)>0)
		{	$row = mysql_fetch_assoc($result);
			if($weekOrMonth==1){
				//echo $row["week"];
				$EbID= $row["week"];
			}
			if($weekOrMonth==2){
				//echo $row["month"];
				$EbID= $row["month"];
			}
		}
		else{
			$query = "INSERT into `Baang`.`usertopreclistenrecord` (`id`,`userid`,`week`,`month`) Values (NULL,$ID,2,1)";
			$result = mysql_query($query);			
			if($weekOrMonth==1){
				$EbID= 2;	// new value of week 
			}
			if($weekOrMonth==2){
				$EbID= 1;	// new value of month 
			}
		}
	}
	else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}	
	return $EbID;
}
?>
