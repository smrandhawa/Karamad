<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$ph = $_REQUEST["ph"];

$db = mysql_connect("localhost","root","");
if (!$db){
	reportDBError("Unable to connect to DB.", __FILE__);
}

echo getResult($ph);

mysql_close($db);	// Close DB connection

function getResult($ph)
{
	$query="SELECT `id` from `Baangsys`.`phnkey` where phonenumber = '$phno'";
	$result=mysql_query($query);
	if($result){
		if(mysql_num_rows($result) == 0){	// Does not exist. Okay, Insert.
			$query="INSERT INTO" .'Baangsys'.".'phnkey' (NULL,$phno)"
			$result = mysql_query($query);
			if ($result){
				$key = mysql_insert_id();
				$queryUser = "INSERT INTO  `Baang`.`user_table` (`userid`,`date_added`) VALUES ($key, NOW());";
				$resultUser = mysql_query($queryUser);
			}
			else{
				reportDBError($query . ". Query Failed.", __FILE__);
			}

		}else{
			$key = mysql_result($result, 0);
		}
	}else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}
	return $key; 
	// Search if it exists.
}
?>