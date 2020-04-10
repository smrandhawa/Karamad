<?PHP
include "..\\wa\\config.php";

if($FreeSwitch=="true"){

	$WshShell = new COM("WScript.Shell");
	if(isset($_REQUEST["name"]))
	{
		$userid = $_REQUEST["userid"];
		$fname = "intro-" . $userid . ".wav";
		$oExec = $WshShell->Run($Audio_Processing_Dir."endpoint.bat" . " " . $Audio_Processing_Dir . " " .$UserIntro_Dir_Baang . " " . $UserIntro_Dir_Baang . " ". $fname, 7, false); 

	}else{
		$rec_id = $_REQUEST["rec_id"];
		$fname = "Rec-" . $rec_id . ".wav";
		update_db_for_recording_exist($rec_id);
		$oExec = $WshShell->Run($Audio_Processing_Dir."endpoint.bat" . " " . $Audio_Processing_Dir . " " . getFilePath($CallRecordings_Dir_Baang, $rec_id, "TRUE") . " " . getFilePath($CallRecordings_Dir_Baang, $rec_id, "TRUE") . " ". $fname, 7, false); 
	}

}else if ($_FILES)
{

	if(isset($_REQUEST["name"]))
	{
		$userid = $_REQUEST["userid"];
		$fname = "intro-" . $userid . ".wav";
		$dest = "D:\\xampp\\htdocs\\Baang\\namesOfUser\\". $fname;
		move_uploaded_file($_FILES['filename']['tmp_name'], $dest);
		$WshShell = new COM("WScript.Shell");
		$oExec = $WshShell->Run($Audio_Processing_Dir."endpoint.bat" . " " . $Audio_Processing_Dir . " " .$UserIntro_Dir_Baang . " " . $UserIntro_Dir_Baang . " ". $fname, 7, false); 
	}else{
		$rec_id = $_REQUEST["rec_id"];
		$fname = "Rec-" . $rec_id . ".wav";
		$dest = "D:\\xampp\\htdocs\\Baang\\Recordings\\". $fname;
		move_uploaded_file($_FILES['filename']['tmp_name'], $dest);
		update_db_for_recording_exist($rec_id);
		$WshShell = new COM("WScript.Shell");
		$oExec = $WshShell->Run($Audio_Processing_Dir."endpoint.bat" . " " . $Audio_Processing_Dir . " " . getFilePath($CallRecordings_Dir_Baang, $rec_id, "TRUE") . " " . getFilePath($CallRecordings_Dir_Baang, $rec_id, "TRUE") . " ". $fname, 7, false); 

	}

}else{ 
	echo "Upload Failed.";
}


function update_db_for_recording_exist($rec_id){
	$db = mysql_connect("localhost","root","");
	if (!$db){
		reportDBError("Unable to connect to DB.", __FILE__);
	}
	$query="UPDATE `Baang`.`recording_table` SET `recordingexist` = '1' WHERE `recording_table`.`Recording_id` = $rec_id;";
	$result = mysql_query($query);
	if ($result){
	}else{
		reportDBError($query . ". Query Failed.", __FILE__);
	}			

	mysql_close($db);	// Close DB connection

}
?>
