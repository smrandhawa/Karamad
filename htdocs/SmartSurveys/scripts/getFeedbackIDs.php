<?PHP
error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$RecID = $_REQUEST["RecID"];
$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult($RecID);
echo serialize($answer);
mysql_close($db);	// Close DB connection

function getResult($RecID)
{

	$query = " SELECT * from `baang`.`feedback_table` where `Recording_id`=$RecID and `softdelete`= 0 order by srno desc";


	$result = mysql_query($query);
	//echo mysql_error();
	//echo $query;
	$IDarray = array();
	if ($result){
			while($row = mysql_fetch_assoc($result)) {
        			//array_push($IDarray, $array($row["srno"],$row["userid"],$row["Recording_id"]));
					$IDarray[] = $row;
    			}
	}
	else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}
	return $IDarray;
}
?>
