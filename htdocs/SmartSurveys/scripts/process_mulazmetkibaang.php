<?PHP

if ($_FILES)
{
	$rec_id = $_REQUEST["rec_id"];
	$fname = "Rec-" . $rec_id . ".wav";
	$dest = "F:\\xampp\\htdocs\\Baang\\JobsRecordings\\". $fname;
	move_uploaded_file($_FILES['filename']['tmp_name'], $dest);
	update_db_for_recording_exist($rec_id);
}else{ 
	echo "Upload Failed.";
}

function update_db_for_recording_exist($rec_id){
	$db = mysql_connect("localhost","root","");
	if (!$db){
		reportDBError("Unable to connect to DB.", __FILE__);
	}
	$query="UPDATE `Baang`.`jobstable` SET `recordingexist` = '1' WHERE `jobstable`.`job_id` = $rec_id;";
	$result = mysql_query($query);
	if ($result){
	}else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}			

	mysql_close($db);	// Close DB connection

}
?>
