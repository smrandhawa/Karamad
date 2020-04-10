<?PHP

error_reporting (0);
header('Cache-Control: no-cache');
$error = 0;

set_time_limit(15);

$ID = $_REQUEST["userid"];

$db = mysql_connect("localhost","root","");
if (!$db){
  reportDBError("Unable to connect to DB.", __FILE__);
}

$answer = getResult($ID);
echo $answer;

mysql_close($db); // Close DB connection

function getResult($ID)
{ 
  $query = "SELECT `type` FROM `Baang`.`blocked_users` WHERE `userid` = '$ID' and `unblocked` = 0";
  $result = mysql_query($query);
  
  if ($result){
     $row = mysql_fetch_assoc($result);
     if(!is_null($row["type"]))
        return $row["type"];
  }else{
    reportDBError($query . ". Query Failed.", __FILE__);
  } 
  return -1;
}
?>
