<?PHP
include "..\\wa\\config.php";

$fbid = $_REQUEST["feedbackid"];
$rec_id = $_REQUEST["rec_id"];
$userid = $_REQUEST["userid"];
$fname = "feedback-" . $fbid . "-u-" . $userid . "-r-" . $rec_id . ".wav";
if($FreeSwitch=="true"){

}else{
	if ($_FILES)
	{
		//Write session to wav files
		$fname = "feedback-" . $fbid . "-u-" . $userid . "-r-" . $rec_id . ".wav";
		$dest = "C:\\xampp\\htdocs\\Baang\\feedbackofpost\\". $fname;
		move_uploaded_file($_FILES['filename']['tmp_name'], $dest);
	}
	else{ 
		echo "Upload Failed.";
	}
}
	$WshShell = new COM("WScript.Shell");
	$oExec = $WshShell->Run($Audio_Processing_Dir."endpoint.bat" . " " . $Audio_Processing_Dir . " " . getFilePath($CallRecordings_Dir_Baang, $fbid, "TRUE") . " " . getFilePath($CallRecordings_Dir_Baang, $fbid, "TRUE") . " ". $fname, 7, false); 

?>
