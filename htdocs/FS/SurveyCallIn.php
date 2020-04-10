<?php

///////////////////////////////////////////////////////////////////////////////////////
set_time_limit(2100);
$start_Time = time();

//temporary log file to what happening
//$myfile = fopen("workinglog.txt", "w") or die("Unable to open file!");
// Explicitly set time zone
$time_zone = "Asia/Karachi";

if (function_exists('date_default_timezone_set')) date_default_timezone_set($time_zone);

// /////////////////////////////////////////////////////////////////////////////////////
// //////////////////////////////// GLOBAL VARIABLES ///////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////////////

$FreeSwitch = "true";
// FreeSwitch Variables

$password = "Conakry2014";
$port = "8021";
$host = "127.0.0.1";
$fp = ""; // Connection handle to FreeSwitch Server
$uuid = "";


$Pollyid = "";
$Country = "PK";
$countryCode = '92';
$channel = "WateenE1";
$SystemLanguage = "Urdu";
$MessageLanguage = "Urdu";
$term = "#";
$newCall = "TRUE";
$hasTheUserRecordedAName = "FALSE"; // User is only prompted to enter his name once per call.
$thiscallStatus = "Answered"; // Temporary assignment
$calltype = "";
$currentStatus = "";
$useridUnCond = ""; // added
$useridUnEnc = ""; // added

$recIDtoPlay = 0;
$effectno = 0;
$ocallid = "";
$ouserid = "";
$workerid = "";
$From = "";
$isBigRecFiles = 0;
$fh = ""; // temprary variable to act as a place holder for file handle
$seqNo = 0;
$logEntry = "";
$callid = "";


// Base Directories
$Drive = "D";
$base_dir = "http://127.0.0.1/wa/";
$scripts_dir = $base_dir . "Scripts/";
$praat_dir = "http://127.0.0.1/wa/Praat/";
$DB_dir = "http://127.0.0.1/wa/DBScripts/";
$logFilePath = $Drive . ":\\xampp\\htdocs\\wa\\KLogs\\";

$polly_base = $Drive . ":\\xampp\\htdocs\\wa\\Praat\\";
$Feedback_Dir = $polly_base . "Feedback\\";
$CallRecordings_Dir = $polly_base . "Recordings\\";

$survey_base = $Drive . ":\\xampp\\htdocs\\SmartSurveys\\";
$Feedback_Dir_Baang = $survey_base . "feedbackofpost\\";
$CallRecordings_Dir_Baang = $survey_base . "recordings\\";
$UserIntro_Dir_Baang = $survey_base . "namesOfUser\\";


$survey_rec_base = $Drive . ":\\xampp\\htdocs\\wa\\SurveyRecordings\\";
$survey_answers = $Drive . ":\\xampp\\htdocs\\wa\\SurveyRecordings\\Answers\\";
$survey_comments = $Drive . ":\\xampp\\htdocs\\wa\\SurveyRecordings\\Comments\\";
$language_demographic_dir = $Drive . ':\\xampp\\htdocs\\wa\\SurveyRecordings\\Language\\';
$profession_demographic_dir = $Drive . ':\\xampp\\htdocs\\wa\\SurveyRecordings\\Profession\\';
$location_demographic_dir = $Drive . ':\\xampp\\htdocs\\wa\\SurveyRecordings\\Location\\';
$disabled_demographic_dir = $Drive . ':\\xampp\\htdocs\\wa\\SurveyRecordings\\Disabled\\';


$Baangpromptsdir = "";
$Polly_prompts_dir = "";

if ($FreeSwitch == "true") {
    $SSpathf = $Drive . ":/xampp/htdocs/SmartSurveys/";
    $promptsBaseDir = $Drive . ":/xampp/htdocs/wa/prompts/"; // absolute hosting
    $praat_dir = $Drive . ":/xampp/htdocs/wa/Praat/";
    $Surveypath = "http://127.0.0.1/SmartSurveys/SSscripts/";
    $Baangpromptsdir = $SSpathf . "prompts/";
    $Surveypromptsdir = $SSpathf . "SSprompts/";
    $Surveyrec_path = $SSpathf . "recordings/";
    $Polly_prompts_dir = $promptsBaseDir . $SystemLanguage . "/Polly/";
}

///////////////////////////////////////////////////////////////////////////////////////

if ($FreeSwitch == "true") {
    $fp = event_socket_create($host, $port, $password);

    if (isset($_REQUEST["uuid"])) {

        $uuid = $_REQUEST["uuid"]; // Comment if Freeswitch is disabled
    }
}
else {
    answer();
}

// /////////////////////////////////////////////////////////////////////////////////////
// ////////////////////////////////// OUTGOING CALLS ///////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////////////

if (isset($_REQUEST["calltype"])) { // This is not incoming call as $calltype is set

    $calltype = $_REQUEST["calltype"];
    $testcall = $_REQUEST["testcall"];
    $channel = $_REQUEST["ch"];
    $userid = $_REQUEST["phno"];
    $oreqid = $_REQUEST["oreqid"];
    $recIDtoPlay = $_REQUEST["recIDtoPlay"];
    $effectno = $_REQUEST["effectno"];
    $ocallid = $_REQUEST["ocallid"];
    $ouserid = $_REQUEST["ouserid"];
    $app = $_REQUEST["app"];
    $From = $_REQUEST["From"];

    $currentStatus = 'InProgress';
    $useridUnEnc = KeyToPh($userid); // ph decoding
    $ouserid = KeyToPh($ouserid); //  Decode the Sender's phone number
    $callid = makeNewCall($oreqid, $userid, $currentStatus, $calltype, $channel); //Create a row in the call table
    $fh = createLogFile($callid);   // commented out for cloud deployment
    

    if (isset($_REQUEST["error"])) {
        $error_type = "";
        $error = $_REQUEST["error"];
        switch ($error) {
            case 'USER_BUSY':
            case 'CALL_REJECTED':
                $error_type = "Busy";
            case 'ALLOTTED_TIMEOUT':
            case 'NO_ANSWER':
            case 'RECOVERY_ON_TIMER_EXPIRE':
                $error_type = "TimedOut";
            case 'NO_ROUTE_DESTINATION':
            case 'INCOMPATIBLE_DESTINATION':
            case 'UNALLOCATED_NUMBER':
                $error_type = "Failed";
            default:
                $error_type = "Error";
        }
        callError($callid, $error_type);
        exit(1);
    }

    $thiscallStatus = 'Answered'; 
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "App: " . $app . ", Call Type: " . $calltype . ", Phone Number: " . $userid . ", Originating Request ID: " . $oreqid . ", Call ID: " . $callid . ", Country: " . $Country . ", ouserid: " . $ouserid . ", Country Code: " . $countryCode);

    if ($channel == "WateenE1") {
        if ($FreeSwitch == "true") {
            if ($calltype == "SSCall-me-back") {
                //StartUpFn();
                Surveyfunction($callid, false);
            }
            else {
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Wrong flow. CallType is $calltype but it should be SSCall-me-back or SSDelivery.");
            }
        }
    }
    else {
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Wrong flow. Channel is $channel but it should be WateenE1.");
    }
}

///////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////// INCOMING CALLS ///////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

else { // Incoming call

    $oreqid = "0";
    $currentStatus = 'InProgress';
    $PollyBaangCallIn = "5000";//change to actual number 
    $Pollyid = calledID(); //which number was called in the current call?
    $Pollyid = trim(preg_replace('/\s\s+/', ' ', $Pollyid));
    $userid = getCallerID();
    $userid = trim(preg_replace('/\s\s+/', ' ', $userid));
    $ouserid = $userid;
    $calltype = 'SSCall-in';
    $app = 'Smart-Surveys';

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $useridUnCond = $userid;
    $useridUnEnc = conditionPhNo($userid, $calltype);
    $useridUnEnc = trim(preg_replace('/\s\s+/', ' ', $useridUnEnc));
    $workerid = PhToWorkerKeyAndStore($useridUnEnc);
    $userid = $workerid;

    $callid = makeNewCall($oreqid, $userid, $currentStatus, $calltype, 'WateenE1'); // Create a row in the call table
    $fh = createLogFile($callid);

    phoneNumBeforeAndAfterConditioning($useridUnCond, $useridUnEnc, $calltype, $userid);

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "App: " . $app . ", Call Type: " . $calltype . ", Phone Number: " . $userid . ", Originating Request ID: " . $oreqid . ", Call ID: " . $callid . ", Country: " . $Country . ", ouserid: " . $ouserid . ", Country Code: " . $countryCode);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "A Call is created with type of BNGCall-in.");

    $thiscallStatus = 'PreAnswer';
    $recIDtoPlay = 0;
    $effectno = 0;
    $ocallid = $callid;
    $ouserid = $userid;
    $From = $userid;

    //StartUpFn();
    //Baangfunction($callid, false);
    Surveyfunction($callid);
}
// Finalize and close (if execution ever reaches this far...)
Prehangup();


///////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// Error Handlers /////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

function callError($callid, $error_type)
{
    global $oreqid;
    global $thiscallStatus;

    $thiscallStatus = $error_type;
    $status = "unfulfilled";
    updateRequestStatus($oreqid, $status);
    Prehangup();
}

function keystimeOutFCN($event)
{
    global $Polly_prompts_dir;
    sayInt($Polly_prompts_dir . "Nobutton.wav");
}

function keysbadChoiceFCN($event)
{
    global $Polly_prompts_dir;
    sayInt($Polly_prompts_dir . "Wrongbutton.wav");
}

function keysbadChoiceFCNG()
{
    global $Polly_prompts_dir;
    sayInt($Polly_prompts_dir . "Wrongbutton.wav");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////Smart Survey Function////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////


function Surveyfunction($callid)
{
    try 
    {
        global $userid;
        global $calltype;
        global $Baangpromptsdir;
        global $Polly_prompts_dir;
        global $Surveypromptsdir;
        global $thiscallStatus;
        global $workerid;

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User entered with id: " . $userid);

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Inside Surveyfunction(). Calltype: " . $calltype);

        //check if users has already done some survey task in the span of one day, If yes then exit by saying sorry
        $status_worked =checklastattemptnotinsameday($workerid);

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Status_Worked_in_Last_24_hours = 1, Status_Worked_in_Last_24_hours = 0, -1 = DB,  error Result Curl: " . $status_worked);

        if ($status_worked == 1)
        {
            sayInt($Surveypromptsdir . "sil250.wav " . $Surveypromptsdir . "Intro.wav " . 
                    $Surveypromptsdir. "ApologNoTaskAvailable.wav ");
            Prehangup();
            exit("Prehangup because worker has already day one task today");
        }

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Playing Survey Welcome and Intro ");

        $welcome_intro_prompt = $Surveypromptsdir . "sil250.wav " . $Surveypromptsdir . "Intro.wav " . 
                                $Surveypromptsdir . "sil250.wav " . $Surveypromptsdir . "BriefDescription.wav ";

        sayInt($welcome_intro_prompt);

        
        $disclaimar_prompt = $Surveypromptsdir . "sil250.wav " . $Surveypromptsdir . "disclaimar_consent_to_participate.wav ";

        sayInt($disclaimar_prompt);
        playBeep();
        
        answerCall();
        
        $thiscallStatus = 'Answered';

        $count = 7; // record anything
        $loop = true;

        while ($loop == true) 
        {
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Prompting User for Main menu choices ");


            $mm_prompt = $Surveypromptsdir . "MainMenuPrompt0.wav ";
            
            $choice = gatherInput($mm_prompt, array(
                "choices" => "[1 DIGITS]",
                "mode" => 'dtmf',
                "bargein"=>1,
                "repeat"=>2,
                "timeout"=>10,
                "onBadChoice"=>"keysbadChoiceFCN",
                "onTimeout"=>"keystimeOutFCN"
            ));


            if ($choice->value == "1") {
                listensurveytasks("recent");
                
            }
            elseif ($choice->value == "2") {
                listensurveytasks("highpaying");
                
            }
            elseif ($choice->value == "3") {
                $done_tasks_type = asktypeofalreadysurveystasks();
                listenalreadydonesurveytasks($done_tasks_type);
            }
            else
            {
                keysbadChoiceFCNG();
            }
        }
    }
    catch(Exception $e) {
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L3", "Error: " . $e->getMessage());
    }

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " is returning.");
}


function listensurveytasks($surveytype)
{
    global $userid;
    global $calltype;
    global $Baangpromptsdir;
    global $Surveypath;
    global $Surveypromptsdir;
    global $Surveyrec_path;
    global $workerid;
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $surveys = array();

    if ($surveytype == "recent")
    {
       $surveys = getrecentsurveys($workerid);
    }
    else
    {
        $surveys = gethighpayingsurveys($workerid);
    }
        
    $sno = 1;//index for surveys
    $iteration = 1;
    //check if empty result then play no surveys
    if (!empty($surveys))
    {
        foreach ($surveys as $row) {

            //check if users has already done some survey task in the span of one day, If yes then exit by saying sorry
            if($iteration > 1)
            {
                $status_worked =checklastattemptnotinsameday($workerid);

                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Status_Worked_in_Last_24_hours = 1, Status_Worked_in_Last_24_hours = 0, -1 = DB,  error Result Curl: " . $status_worked);

                if ($status_worked == 1)
                {
                    sayInt($Surveypromptsdir . "sil250.wav " . $Surveypromptsdir . "Intro.wav " . 
                            $Surveypromptsdir. "ApologNoTaskAvailable.wav ");
                    Prehangup();
                    exit("Prehangup because worker has already day one task today");
                }
            }

            $new_task_alert = "new_task_alert.wav ";
            $task_no = strval($sno)."_kam.wav ";
            $brief_desc = "Survey_Description_" . $row['survey_id'] . "_" . $row['file_name_description'] . " ";
            $instruct_workers = "Survey_Instructions_" . $row['survey_id'] . "_" . $row['file_name_instructions'] . " ";
            $min_reward = intval($row['min_reward_per_response']);
            $max_reward = intval($row['max_reward_per_response']);
            $reward = "";
            if($min_reward == $max_reward)
            {
                $reward = $row['min_reward_per_response'] . "_rupees.wav ";
            }
            else
            {
                $reward = $row['min_reward_per_response'] ."_to_". $row['max_reward_per_response']. "_rupees.wav ";
            }
            

            if ($surveytype == "recent")
            {
                $survey_task =  $Surveypromptsdir . $new_task_alert .
                                $Surveypromptsdir . $task_no .
                                $Surveyrec_path . $brief_desc . 
                                $Surveypromptsdir . "ye_kam.wav " . 
                                $Surveypromptsdir . $reward .  
                                $Surveypromptsdir . "ka_hai.wav ";
            }
            else
            {
                $survey_task =  $Surveypromptsdir . $new_task_alert .
                                $Surveypromptsdir . $reward . 
                                $Surveypromptsdir . "ke_liye.wav " .
                                $Surveypromptsdir . $task_no .
                                $Surveyrec_path . $brief_desc;
            }
            sayInt($survey_task);
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Task : " . $survey_task);

            $count = 2;
            $loop = true;

            while ($loop == true) 
            {

                if($count < 0)
                {
                    Prehangup();
                    exit("Prehangup because user was not responding or its responses were all invalid in more than 3 retires");
                }
                //survey menu prompt
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Prompting User for Survey menu choices ");


                $sm_prompt = $Surveypromptsdir . "surveymenuprompt.wav ";
                
                $choice = gatherInput($sm_prompt, array(
                    "choices" => "[1 DIGITS]",
                    "mode" => 'dtmf',
                    "bargein"=>1,
                    "repeat"=>2,
                    "timeout"=>10,
                    "onBadChoice"=>"keysbadChoiceFCN",
                    "onTimeout"=>"keystimeOutFCN"
                ));

                $invalid_choices = array("4", "5", "6", "7", "8", "9", "0");

                if ($choice->value == "1") {
                    $task_details = $Surveypromptsdir . "task_start_alert.wav " .
                                    $Surveypromptsdir . "task_details.wav " .
                                    $Surveyrec_path . $instruct_workers;
                    sayInt($task_details);
                    //attempt survey here
                    attemptsurveytask($row['survey_id']);
                    $sno = $sno + 1;
                    $iteration = $iteration + 1;
                    $loop = false;
                }
                elseif ($choice->value == "2") {
                    //listen to next tasks
                    $sno = $sno + 1;
                    $loop = false;
                }
                elseif ($choice->value == "3") {
                    //go back to main menu
                    return;
                }
                else
                {
                    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Choices value = " . $choice->value);
                    if (in_array($choice->value, $invalid_choices))
                    {
                        $count = $count - 1;
                        
                        if ($count >= 0){
                            keysbadChoiceFCNG();                            
                        }     
                    }
                    else
                    {
                        $count = 0-1;
                    }
                }
            }
        }
    }
    else
    {
        sayInt($Surveypromptsdir. "ApologNoTaskAvailable.wav ");
        Prehangup();
        exit("Prehangup because No task is available for workers");
    }
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function select_choice($survey_response_id, $qrow, $choice_options, $breakingstate)
{
    global $Surveypromptsdir;
    global $Surveyrec_path;
    global $Baangpromptsdir;
    global $userid;
    global $workerid;

    $sizeofchoices = sizeof($choice_options);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Size of choices : " . strval($sizeofchoices));
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "last choice row : " . implode( ", ", $choice_options[$sizeofchoices-1]));

    $choices_ids = array();
    $cno = 1;
    $possible_inputs = array("1","2","3","4", "5", "6", "7", "8", "9", "0");
    $valid_inputs_gen = array();
    $invalid_inputs_gen = array();
    $choices_prompt_array = array();
    $si = 0;
    $ei = $sizeofchoices;
    if ($breakingstate == "before") {
        $ei = (int)($sizeofchoices/2);
    }
    elseif ($breakingstate == "after") {
        $si = (int)($sizeofchoices/2);
        $ei = $sizeofchoices - 1;
    }

    for ($i=$si; $i < $ei; $i++) 
    { 
        $choices_ids[] = $choice_options[$i]['choice_id'];
        $choices_prompt_array[] = $Surveypromptsdir . strval($cno)."_option.wav ";
        $choices_prompt_array[] = $Surveypromptsdir . "sil250.wav ";
        $choice_rec = $Surveyrec_path . "Choice_" . $choice_options[$i]['choice_id'] . "_" . $choice_options[$i]['file_name'] . " ";
        $choices_prompt_array[] = $choice_rec;
        
        if($qrow['generic'] == 0)
        {
            $choices_prompt_array[] = $Surveypromptsdir . "sil250.wav ";
            $choices_prompt_array[] = $Surveypromptsdir . "info_multi_2.wav ";
        }
        
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Question Row JSON : " . implode( ", ", $qrow ));

        $choices_prompt_array[] = $Surveypromptsdir . "sil250.wav ";
        $choices_prompt_array[] = $Surveypromptsdir . strval($cno)."_dubain.wav ";
        $choices_prompt_array[] = $Surveypromptsdir . "sil250.wav ";
        $valid_inputs_gen[] = strval($cno);
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Choice JSON : " . implode( ", ", $choice_options[$i] ));
        $cno++;
    }

    if ($breakingstate != "none") 
    {
        $choices_ids[] = $choice_options[$sizeofchoices-1]['choice_id'];

        $choices_prompt_array[] = $Surveypromptsdir . strval($cno)."_option.wav ";
        $choices_prompt_array[] = $Surveypromptsdir . "sil250.wav ";

        $choice_rec = $Surveypromptsdir . "kisi_aur_wajah_se.wav ";

        if($breakingstate == "after") 
        {
            $choice_rec = $Surveypromptsdir . "agr_inn_option_mein_bi_wo_waja_nai_hai_to.wav ";
        }

        $choices_prompt_array[] = $choice_rec;

        if($breakingstate != "after")
        {
            if($qrow['generic'] == 0)
            {
                
                $choices_prompt_array[] = $Surveypromptsdir . "sil250.wav ";
                $choices_prompt_array[] = $Surveypromptsdir . "info_multi_2.wav ";
            }
        }

        $choices_prompt_array[] = $Surveypromptsdir . "sil250.wav ";
        $choices_prompt_array[] = $Surveypromptsdir . strval($cno)."_dubain.wav ";
        $choices_prompt_array[] = $Surveypromptsdir . "sil250.wav ";
        $valid_inputs_gen[] = strval($cno);

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Choice JSON : " . implode( ", ", $choice_options[$sizeofchoices-1] ));
    }

    foreach ($possible_inputs as $input) 
    {
        if(!in_array($input, $valid_inputs_gen))
        {
            $invalid_inputs_gen[] = $input;
        }

    }

    $choices_prompt_string = "";
    
    foreach ($choices_prompt_array as $prompt) {
        $choices_prompt_string .= $prompt;
    }
    
    $count = 2;
    $loop = true;

    while ($loop == true) 
    {

        if($count < 0)
        {
            Prehangup();
            exit("Prehangup because user was not responding or its responses were all invalid in more than 3 retires");
        }

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Prompting User to select from answer option choices ");
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Prompt String using Generic method : ".$choices_prompt_string);

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Valid inputs using long method : ".json_encode($valid_inputs_gen));

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "InValid inputs using gen method : ".json_encode($invalid_inputs_gen));

        $choice = gatherInput($choices_prompt_string, array(
            "choices" => "[1 DIGITS]",
            "mode" => 'dtmf',
            "bargein"=>1,
            "repeat"=>2,
            "timeout"=>10,
            "onBadChoice"=>"keysbadChoiceFCN",
            "onTimeout"=>"keystimeOutFCN"
        ));

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Choice value = " . $choice->value);
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Choices IDS : ".json_encode($choices_ids));
        if (in_array($choice->value, $valid_inputs_gen))
        {
            //$selected_option_choice = strval($choice->value)."_option.wav ";
            //sayInt($Surveypromptsdir. $selected_option_choice);
            if ($breakingstate == "before") {
                if(intval($choice->value)-1 == (int)($sizeofchoices/2))
                {
                    $info_other_selected = $Surveypromptsdir ."aap_ne_kaha_hai.wav ";
                    $choice_other = $Surveyrec_path . "Choice_" . $choice_options[$sizeofchoices-1]['choice_id'] . "_" . $choice_options[$sizeofchoices-1]['file_name'] . " ";
                    $info_more = $Surveypromptsdir ."kia_wo_wajah_inn_option_mein_se_hai.wav ";
                    sayInt($info_other_selected.$choice_other.$info_more);
                    select_choice($survey_response_id, $qrow, $choice_options, "after");

                }
                else
                {
                    insertanswer($survey_response_id,$qrow['question_id'],$choices_ids[intval($choice->value)-1]);
                }
            }
            else
            {
                insertanswer($survey_response_id,$qrow['question_id'],$choices_ids[intval($choice->value)-1]);
            }

            $loop = false;

         }
        else
        {
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Choice value = " . $choice->value);
            if (in_array($choice->value, $invalid_inputs_gen))
            {
                $count = $count - 1;
                
                if ($count >= 0){
                    keysbadChoiceFCNG();                            
                }     
            }
            else
            {
                $count = 0-1;
            }
        }
    }


}
function attemptsurveytask($survey_id)
{
    global $Surveypromptsdir;
    global $Surveyrec_path;
    global $Baangpromptsdir;
    global $userid;
    global $workerid;
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $survey_response_id = intiatesurveyresponse($survey_id, $userid);

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Survey Response ID : " . $survey_response_id);

    $questions = array();

    $questions = getquestionssurvey($survey_id);
        
    $qno = 1;//index for surveys
    //check if empty result then play no surveys
    if (!empty($questions))
    {
        foreach ($questions as $qrow) {

            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Question JSON : " . json_encode($qrow));

            $dependence_filter = intval(checkdependencies($survey_response_id,$qrow['question_id']));
            if($dependence_filter)
            {
                $question_no = strval($qno)."_sawaal.wav ";
                $question_rec = "Question_" . $qrow['question_id'] . "_" . $qrow['file_name'] . " ";
                $question = $Surveypromptsdir . $question_no . $Surveypromptsdir . "sil250.wav " .
                            $Surveyrec_path . $question_rec . $Surveypromptsdir . "sil250.wav ";

                sayInt($question);
                
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Question : " . $question);

                if ($qrow['question_type'] == "radio")
                {
                    $choice_options = array();
                    $choice_options = getchoicesquestion($qrow['question_id']);
                    $choices_ids = array(); 

                    if($qrow['generic'] == 0)
                    {
                        $gen_info = $Surveypromptsdir ."info_multi_1.wav ";
                        sayInt($gen_info);
                    }

                    if(!empty($choice_options))
                    {
                        if (sizeof($choice_options) > 9) 
                        {
                            select_choice($survey_response_id, $qrow, $choice_options, "before");
                        }
                        else
                        {
                            select_choice($survey_response_id, $qrow, $choice_options, "none");
                        }
                    }
                }
                else
                {
                    $gen_info = $Surveypromptsdir ."info_open_end_sawaal_1.wav ";
                    sayInt($gen_info);

                    global $survey_answers;
                    global $Surveypath;
                    
                    $rerecord = true;
                    while ($rerecord) {

                        $question_id = $qrow['question_id'];
                        $survey_answer_id = insertanswer($survey_response_id,$question_id,0);

                        recordAudio($Surveypromptsdir . "info_open_end_sawaal_2.wav", array(
                            "beep"=>1,
                            "timeout"=>30,
                            "bargein"=>0,
                            "silenceTimeout"=>4,
                            "maxTime"=>60,
                            "terminator" => "#",
                            "format" => "audio/wav",
                            "recordURI" => $Surveypath . "insert_question_answer.php?srid=$survey_response_id&qid=$question_id&cid=0",
                        ));

                        sayInt($Baangpromptsdir . "aap_nay_ye_record_karwaya_hai.wav");

                        $filefolder = $survey_answer_id - ($survey_answer_id % 1000);
                        $path = $survey_answers . $filefolder . "/" . $survey_answer_id . ".wav";
                        $path = str_replace("\\", "/", $path);
                        sayInt($path);

                        $result2 = gatherInput($Baangpromptsdir . "agar_ye_theek_hai.wav ", array(
                            "choices" => "[1 DIGITS]",
                            "mode" => 'dtmf',
                            "bargein" => false,
                            "repeat" => 2,
                            "timeout" => 10,
                            "onBadChoice" => "keysbadChoiceFCN",
                            "onTimeout" => "keystimeOutFCN"
                        ));

                        if ($result2->value == "1") {
                            $rerecord = false;
                            //insertanswer($survey_response_id,$qrow['question_id'],$choices_ids[intval($choice->value)-1]);
                        }
                    }
                }

                $qno = $qno + 1;
            }
        }

        //check if user responds to all the questions then mark it complete
        marktaskcompleted($survey_response_id);

        getcarrierinfo($survey_response_id);
        //ask for feedback
        gettaskfeedback($survey_response_id);
    }
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function asktypeofalreadysurveystasks()
{
    global $userid;
    global $calltype;
    global $Baangpromptsdir;
    global $Surveypath;
    global $Surveypromptsdir;
    global $Surveyrec_path;
    global $workerid;
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");


    $count = 2;
    $loop = true;

    while ($loop == true) 
    {

        if($count < 0)
        {
            Prehangup();
            exit("Prehangup because user was not responding or its responses were all invalid in more than 3 retires");
        }
        //survey menu prompt
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Prompting User to listen paid or unpaid surveys he did ");


        $sm_prompt = $Surveypromptsdir . "DoneTasksPrompt.wav ";
        
        $choice = gatherInput($sm_prompt, array(
            "choices" => "[1 DIGITS]",
            "mode" => 'dtmf',
            "bargein"=>1,
            "repeat"=>2,
            "timeout"=>10,
            "onBadChoice"=>"keysbadChoiceFCN",
            "onTimeout"=>"keystimeOutFCN"
        ));

        $invalid_choices = array("3","4", "5", "6", "7", "8", "9", "0");

        if ($choice->value == "1") {
            return "paid";
            $loop = false;

        }
        elseif ($choice->value == "2") {
            return "unpaid";
            $loop = false;
        }
        else
        {
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Choices value = " . $choice->value);
            if (in_array($choice->value, $invalid_choices))
            {
                $count = $count - 1;
                
                if ($count >= 0){
                    keysbadChoiceFCNG();                            
                }     
            }
            else
            {
                $count = 0-1;
            }
        }
    }

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function listenalreadydonesurveytasks($donesurveystype)
{
    global $userid;
    global $calltype;
    global $Baangpromptsdir;
    global $Surveypath;
    global $Surveypromptsdir;
    global $Surveyrec_path;
    global $workerid;
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $surveys = array();

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " donesurveystype : " . $donesurveystype);

    $paidsurveys = array();
    $paidsurveys = getpaidsurveys($workerid);
    
    $unpaidsurveys = array();
    $unpaidsurveys = getunpaidsurveys($workerid);

    if ($donesurveystype == "paid")
    {
       $surveys = $paidsurveys;
    }
    else
    {
        $surveys = $unpaidsurveys;
    }
        
    $sno = 1;//index for surveys
    
    //check if empty result then play no surveys
    if (!empty($surveys))
    {
        foreach ($surveys as $row) {

            $new_task_alert = "DoneTasksSperatorAlert.wav ";
            //$task_no = strval($sno)."_kam.wav ";
            $brief_desc = "Survey_Description_" . $row['survey_id'] . "_" . $row['file_name_description'] . " ";
            //$reward = $row['reward_per_response'] . "_rupee.wav ";
            $min_reward = intval($row['min_reward_per_response']);
            $max_reward = intval($row['max_reward_per_response']);
            $reward = "";
            if($min_reward == $max_reward)
            {
                $reward = $row['min_reward_per_response'] . "_rupees.wav ";
            }
            else
            {
                $reward = $row['min_reward_per_response'] ."_to_". $row['max_reward_per_response']. "_rupees.wav ";
            }

            if ($donesurveystype == "paid")
            {
                $survey_task =  $Surveypromptsdir . $new_task_alert .
                                $Surveypromptsdir . $reward . 
                                $Surveypromptsdir . "DidTaskFor.wav " .
                                //$Surveypromptsdir . $task_no .
                                $Surveyrec_path . $brief_desc .
                                $Surveypromptsdir . "DoneTaskPaidAlready.wav " ;
            }
            else
            {
                $survey_task =  $Surveypromptsdir . $new_task_alert .
                                $Surveypromptsdir . $reward . 
                                $Surveypromptsdir . "DidTaskFor.wav " .
                                //$Surveypromptsdir . $task_no .
                                $Surveyrec_path . $brief_desc .
                                $Surveypromptsdir . "DoneTaskYettobePaid.wav " ;
            }
            sayInt($survey_task);
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Task : " . $survey_task);
        }
    }
    else
    {
        $bothempty = false;

        if (empty($unpaidsurveys) && empty($paidsurveys))
        {
            $bothempty = true;
            sayInt($Surveypromptsdir. "ApologyNoTaskDone.wav ");
        }

        if ($donesurveystype == "paid")
        {
            if($bothempty == false)
            {
                sayInt($Surveypromptsdir. "ApologyPaidDoneTask.wav ");
            }
        }
        else
        {
            if($bothempty == false)
            {
                sayInt($Surveypromptsdir. "ApologyNoUnPaidDoneTask.wav ");
            }
        }
    }
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

/*
function getcarrierinfo($survey_response_id)
{
    global $Surveypromptsdir;
    global $Surveyrec_path;
    global $Baangpromptsdir;
    global $userid;

    global $Surveypath;

    global $workerid;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $carrier_entered = doCurl($Surveypath . "check_network_entered.php?wid=$workerid" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Carrier entered = 1, Carrier never entered before = 0 Result Curl: " . $carrier_entered);

    if ($carrier_entered == "0")
    {
        $carrier_info = $Surveypromptsdir ."CarrierInfo.wav ";
        $carrier_choices = $Surveypromptsdir ."CarrierChoicesPrompt.wav ";

        $cc_prompt =  $carrier_info . $Surveypromptsdir . "sil250.wav " . $carrier_choices; 

        $count = 2;
        $loop = true;

        while ($loop == true) 
        {

            if($count < 0)
            {
                Prehangup();
                exit("Prehangup because user was not responding or its responses were all invalid in more than 3 retires");
            }
            //survey menu prompt
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Prompting User for Survey menu choices ");
            
            $choice = gatherInput($cc_prompt, array(
                "choices" => "[1 DIGITS]",
                "mode" => 'dtmf',
                "bargein"=>1,
                "repeat"=>2,
                "timeout"=>10,
                "onBadChoice"=>"keysbadChoiceFCN",
                "onTimeout"=>"keystimeOutFCN"
            ));

            $invalid_choices = array( "6", "7", "8", "9", "0");

            if ($choice->value == "1") {
                setcarrier("Jazz");
                sayInt($Surveypromptsdir ."CarrierShukriya.wav ");
                $loop = false;
            }
            elseif ($choice->value == "2") {
                setcarrier("Warid");
                sayInt($Surveypromptsdir ."CarrierShukriya.wav ");
                $loop = false;
            }
            elseif ($choice->value == "3") {
                setcarrier("Telenor");
                sayInt($Surveypromptsdir ."CarrierShukriya.wav ");
                $loop = false;
            }
            elseif ($choice->value == "4") {
                setcarrier("Zong");
                sayInt($Surveypromptsdir ."CarrierShukriya.wav ");
                $loop = false;
            }
            elseif ($choice->value == "5") {
                setcarrier("Ufone");
                sayInt($Surveypromptsdir ."CarrierShukriya.wav ");
                $loop = false;
            }
            else
            {
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Choices value = " . $choice->value);
                if (in_array($choice->value, $invalid_choices))
                {
                    $count = $count - 1;
                    
                    if ($count >= 0){
                        keysbadChoiceFCNG();                            
                    }     
                }
                else
                {
                    $count = 0-1;
                }
            }
        }
    }
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function setcarrier($carrier)
{
    global $Surveypath;
    global $workerid;
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $result = doCurl($Surveypath . "set_carrier.php?wid=$workerid&cir=$carrier" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Success = 1, Failure = 0 Result Curl: " . $result);

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}
*/

function getcarrierinfo($survey_response_id)
{
    global $Surveypromptsdir;
    global $Surveyrec_path;
    global $Baangpromptsdir;
    global $userid;
    global $Surveypath;
    global $workerid;
    global $useridUnEnc;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $carrier_entered = doCurl($Surveypath . "check_network_entered.php?wid=$workerid" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Carrier entered = 1, Carrier never entered before = 0 Result Curl: " . $carrier_entered);
        //carrier_entered = 0 Phone number not yet processed
        //carrier_entered = 1 Valid Phone number and carrier
        //carrier_entered = -1 if user phone number is not valid or error in getting carrier entered from db
        //carrier_entered = -2 if we are out of balance for hrl lookup thing or curl error in looking up balance
        //carrier_entered = -3 if curl error
    if ($carrier_entered == 0 || $carrier_entered == -2 || $carrier_entered == -3 )
    {
        //$useridUnEnc = '03222385416';
        //get balance
        $balance = doCurl($Surveypath . "get_balance.php" );
        if($balance > 0)
        {
            $carrier = doCurl($Surveypath . "get_carrier.php?phno=$useridUnEnc" );
            if($carrier =="error_in_curl_request" || $carrier == "error_number_not_valid")
            {
                if($carrier =="error_in_curl_request")
                {
                    $carrier_entered = -3;
                }
                else
                {
                    $carrier_entered = -1;
                }
            }
            else
            {
                $carrier_entered = 1;
            }
        }
        elseif($balance == 0)
        {
            $carrier = "error_no_balance";
            $carrier_entered = -2;
        }
        else//balance == -1
        {
            $carrier = "error_in_curl_request";
            $carrier_entered = -3;
        }

        setcarrierattributes($carrier,$carrier_entered);

    }
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function setcarrierattributes($carrier,$carrier_entered)
{
    global $Surveypath;
    global $workerid;
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $result = doCurl($Surveypath . "set_carrier_attributes.php?wid=$workerid&cir=$carrier&cire=$carrier_entered" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Success = 1, Failure = 0 Result Curl: " . $result);

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function marktaskcompleted($survey_response_id)
{
    global $Surveypromptsdir;
    global $Surveyrec_path;
    global $Baangpromptsdir;
    global $userid;

    global $Surveypath;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $affirm_task_completed = $Surveypromptsdir ."Affirm.wav ";
    sayInt($affirm_task_completed);

    $result = doCurl($Surveypath . "mark_survey_complete.php?srid=$survey_response_id" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Success = 1, Failure = 0 Result Curl: " . $result);

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}
function gettaskfeedback($survey_response_id)
{
    global $Surveypromptsdir;
    global $Surveyrec_path;
    global $Baangpromptsdir;
    global $userid;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
    
    $count = 3;
    $loop = true;

    while ($loop == true) 
    {

        if($count ==0)
        {
            Prehangup();
            exit("Prehangup because user was not responding or its responses were all invalid in more than 3 retires");
        }
        //survey menu prompt
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Prompting User for Survey menu choices ");


        $fb_prompt = $Surveypromptsdir . "FeedbackPrompt.wav ";
        
        $choice = gatherInput($fb_prompt, array(
            "choices" => "[1 DIGITS]",
            "mode" => 'dtmf',
            "bargein"=>1,
            "repeat"=>2,
            "timeout"=>10,
            "onBadChoice"=>"keysbadChoiceFCN",
            "onTimeout"=>"keystimeOutFCN"
        ));

        $invalid_choices = array("5", "6", "7", "8", "9", "0");

        if ($choice->value == "1") {
            
            insertfeedback($survey_response_id,"like");
            $loop = false;
            $thanks_for_feedback = $Surveypromptsdir ."Shukriya.wav ";
            sayInt($thanks_for_feedback);

        }
        elseif ($choice->value == "2") {
            
            insertfeedback($survey_response_id,"dislike");
            $loop = false;
            $thanks_for_feedback = $Surveypromptsdir ."Shukriya.wav ";
            sayInt($thanks_for_feedback);
            
        }
        elseif ($choice->value == "3") {
            
            insertfeedback($survey_response_id,"report");
            $loop = false;
            $thanks_for_feedback = $Surveypromptsdir ."Shukriya.wav ";
            sayInt($thanks_for_feedback);
        }
        elseif ($choice->value == "4") {
            
            $loop = false;
            global $survey_comments;
            global $Surveypath;
                
                $rerecord = true;
                while ($rerecord) {

                    $survey_feedback_id = insertfeedback($survey_response_id,"comment");

                    recordAudio($Surveypromptsdir . "RecordReport.wav", array(
                        "beep"=>1,
                        "timeout"=>30,
                        "bargein"=>0,
                        "silenceTimeout"=>4,
                        "maxTime"=>60,
                        "terminator" => "#",
                        "format" => "audio/wav",
                        "recordURI" => $Surveypath . "insert_feedback.php?srid=$survey_response_id&pref=comment",
                    ));

                    sayInt($Baangpromptsdir . "aap_nay_ye_record_karwaya_hai.wav");

                    $filefolder = $survey_feedback_id - ($survey_feedback_id % 1000);
                    $path = $survey_comments . $filefolder . "/" . $survey_feedback_id . ".wav";
                    $path = str_replace("\\", "/", $path);
                    sayInt($path);

                    $result2 = gatherInput($Baangpromptsdir . "agar_ye_theek_hai.wav ", array(
                        "choices" => "[1 DIGITS]",
                        "mode" => 'dtmf',
                        "bargein" => false,
                        "repeat" => 2,
                        "timeout" => 10,
                        "onBadChoice" => "keysbadChoiceFCN",
                        "onTimeout" => "keystimeOutFCN"
                    ));

                    if ($result2->value == "1") {
                        $rerecord = false;
                        //insertanswer($survey_response_id,$qrow['question_id'],$choices_ids[intval($choice->value)-1]);
                    }
                }

            $thanks_for_feedback = $Surveypromptsdir ."Shukriya.wav ";
            sayInt($thanks_for_feedback);
        }
        else
        {
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Choice value = " . $choice->value);
            if (in_array($choice->value, $invalid_choices))
            {
                $count = $count - 1;
                
                if ($count >= 0){
                    keysbadChoiceFCNG();                            
                }     
            }
            else
            {
                $count = 0-1;
            }
        }
    }

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function insertfeedback($survey_response_id, $preference)
{
    global $Surveypath;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $result = doCurl($Surveypath . "insert_feedback.php?srid=$survey_response_id&pref=$preference" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Result: " . $result); 
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

    return $result;
}

function insertanswer($survey_response_id, $question_id,$choice_id)
{
    global $Surveypath;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $result = doCurl($Surveypath . "insert_question_answer.php?srid=$survey_response_id&qid=$question_id&cid=$choice_id" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Result: " . $result); 
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

    return $result;
}

function intiatesurveyresponse($survey_id, $userid)
{
    global $Surveypath;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $result = doCurl($Surveypath . "intiate_survey_response.php?sid=$survey_id&uid=$userid" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Result: " . $result); 
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

    return $result;
}


function getchoicesquestion($question_id)
{

    global $Surveypath;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $result = doCurl($Surveypath . "get_choices_of_question.php?qid=$question_id" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: " . $result);
    
    $result = json_decode($result, true);
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

    return $result;

}

function getquestionssurvey($survey_id)
{

    global $Surveypath;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $result = doCurl($Surveypath . "get_questions_of_survey.php?sid=$survey_id" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: " . $result);
    
    $result = json_decode($result, true);
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

    return $result;

}

function checklastattemptnotinsameday($workerid)
{
    global $Surveypath;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $result = doCurl($Surveypath . "check_if_survey_attempted_within_last_twenty_four_hours.php?wid=$workerid");
      
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

    return $result;
}

function checkdependencies($survey_response_id,$question_id)
{

    global $Surveypath;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $result = doCurl($Surveypath . "check_question_dependency.php?srid=$survey_response_id&qid=$question_id");
      
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

    return $result;

}

function getpaidsurveys($workerid)
{

    global $Surveypath;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $result = doCurl($Surveypath . "get_paid_surveys.php?wid=$workerid" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: " . $result);
    
    $result = json_decode($result, true);
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

    return $result;

}

function getunpaidsurveys($workerid)
{

    global $Surveypath;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $result = doCurl($Surveypath . "get_unpaid_surveys.php?wid=$workerid" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: " . $result);
    
    $result = json_decode($result, true);
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

    return $result;

}

function getrecentsurveys($workerid)
{

    global $Surveypath;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $result = doCurl($Surveypath . "get_recent_surveys.php?wid=$workerid" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: " . $result);
    
    $result = json_decode($result, true);
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

    return $result;

}


function gethighpayingsurveys($workerid)
{
    global $Surveypath;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

    $result = doCurl($Surveypath . "get_high_paying_surveys.php?wid=$workerid" );
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: " . $result);
    
    $result = json_decode($result, true);
    
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

    return $result;
}

///////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// Tropo Library //////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

function writeToLog($id, $handle, $tag, $str)
{
    global $seqNo;
    global $userid;
    global $fh;

    $writeToTropoLogs = "true";
    $spch1 = "%%";
    $spch2 = "$$";
    $del = "~";
    $colons = ":::";
    // From Apr 01, 2015: tag could be L0: System level, L1: Mixed interest, L2: User Experience
    if($tag!= 'L0' && $tag!= 'L1' && $tag!= 'L2'){
        $tag = 'L1';
    }
    if($id == "" || $id == 0){
        $id = 'UnAssigned';
    }

    $now = new DateTime;
    $actualLogLine = 'WateenE1' . $del . $id . $del . $seqNo . $del . $now->format('D_Y-m-d_H-i-s') . $del . $tag . $colons . $del . $str;
    $string = $spch1 . $spch2  . $del . $actualLogLine . $spch2 . $spch1;

    fwrite($fh, $string . "\n");
    fflush($fh);
}

function StartUpFn()
{
    global $userid;
    global $calltype;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " entered.");

    $status = "InProgress";
    updateCallStatus($GLOBALS['callid'], $status);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Call Status changed to InProgress.");

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " returning.");
}

function Prehangup()
{
    global $callid;
    global $fh;
    global $thiscallStatus;
    global $currentCall;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " entered.");

    updateCallStatus($callid, $thiscallStatus);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Hanging Up. Call ended for callid: " . $callid);
    markCallEndTime($callid);
    hangupFT();
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " exiting and ending the script.");
    fclose($fh);
    exit(0);
}

function isThisCallActive()
{
    global $FreeSwitch;
    global $uuid;
    global $fp;
    global $callid;
    global $calltype;
    global $fh;
    global $start_Time;

    $retVal = "";
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " entered.");

    if ($FreeSwitch == "false") {
        $retVal = $currentCall->isActive;
    }
    else {
        $cmd = "api lua isActive.lua " . $uuid;
        $response = event_socket_request($fp, $cmd);
        $retVal = trim($response);
    }

    writeToLog($callid, $fh, "isActive", "Is the current call active? " . $retVal . ". Hanging up if the call is not active.");

    if ($retVal == "false") {
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " calling Prehangup function to hangup and terminate the script.");
        Prehangup();
    }

    $current_Time = time();
    $time_elapsed = $current_Time - $start_Time;
    writeToLog($callid, $fh, "isActive", "Call Duration: " . $time_elapsed);

    if ($calltype != "BNGCall-in" && $time_elapsed >= 900) {
        writeToLog($callid, $fh, "isActive", "Call Type: " . $calltype ." != BNGCall-in.");
        writeToLog($callid, $fh, "isActive", "Call Duration is more than 15 minutes hanging up: " . $time_elapsed);
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " calling Prehangup function to hangup due to overtime and terminate the script.");
        Prehangup();
    }

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " returning with value : " . $retVal);
    return $retVal;
}

function calledID()
{
    global $fp;
    global $FreeSwitch;

    if ($FreeSwitch == "false") {
        global $currentCall;

        return $currentCall->calledID;
    }
    else {
        global $uuid;

        $cmd = "api lua getCalledID.lua " . $uuid;
        $response = event_socket_request($fp, $cmd);
        return $response;
    }
}

function getSessID()
{
    global $FreeSwitch;

    if ($FreeSwitch == "false") {
        global $currentCall;
        return $currentCall->sessionId;
    }
    else {
        global $uuid;
        return $uuid;
    }
}

function getCallerID()
{
    global $FreeSwitch;

    if ($FreeSwitch == "false") {
        global $currentCall;

        $useridUnclean = $currentCall->getHeader("from");// Getting the User's Phone Number from the sip header
        $Useless = array(
            "<", ">", "@", ";", "_"
        );
        $Clean = str_replace($Useless, "&", $useridUnclean);
        $colon = array(
            ":"
        );
        $equals = str_replace($colon, "=", $Clean);
        parse_str($equals);
        $userid = $sip; // Phone number acquired

        $useridBfTrim = $userid;
        $userid = trim($userid, " \t\n\r\0\x0B");
        $useridAfTrim = $userid;
        $userCallerID = $currentCall->callerID;

        if ($userid == "") {
            $userid = $currentCall->callerID;
        }

        return $userid;
    }
    else {
        global $uuid;
        global $fp;

        $cmd = "api lua getCallerID.lua " . $uuid;
        $response = event_socket_request($fp, $cmd);
        return $response;
    }
}

function answerCall()
{
    global $FreeSwitch;

    if ($FreeSwitch == "false") {
        answer();
    }
    else {
        global $uuid;
        global $fp;

        $cmd = "api lua answer.lua " . $uuid; //first character is a null (0)
        $response = event_socket_request($fp, $cmd);
        return $response;
    }
}

function rejectCall($app)
{
    global $FreeSwitch;

    if ($FreeSwitch == "false") {

        reject();
        Prehangup();
    }
    else {
        global $uuid;
        global $fp;
        global $fh;

        $cmd = "api lua reject.lua " . $uuid; //first character is a null (0)
        $response = event_socket_request($fp, $cmd);
        Prehangup();
    }
}

// /////////////////////////////////////////////////////////////////////////////////////
// //////////////////////////////// Polly Game /////////////////////////////////////////
// /////////////////////////////////////////////////////////////////////////////////////
// /deleted all polly functions
// ////////////////////////////////////////////////////////////////////////////////////
// /////////////////////////// DB Access Functions ////////////////////////////////////
// ////////////////////////////////////////////////////////////////////////////////////

function makeNewRec($callid)
{
    global $DB_dir;

    $url = $DB_dir . "New_Rec.php?callid=$callid";
    $result = doCurl($url);
    return $result;
}

function makeNewCall($reqid, $phno, $status, $calltype, $chan)
{
    global $Surveypath;

    $url = $Surveypath . "New_Call.php?reqid=$reqid&phno=$phno&calltype=$calltype&status=$status&ch=$chan";
    $result = doCurl($url);
    return $result;
}

function makeNewReq($recid, $effect, $callid, $reqtype, $phno, $status, $syslang, $msglang, $ch)
{
    global $DB_dir;
    global $userid;
    global $testcall;

    $url = $DB_dir . "New_Req.php?recid=$recid&effect=$effect&callid=$callid&reqtype=$reqtype&from=$userid&phno=$phno&status=$status&syslang=$syslang&msglang=$msglang&testcall=$testcall&ch=$ch";
    $result = doCurl($url);
    return $result;
}

function createMissedCall($recid, $effect, $callid, $reqtype, $phno, $status, $syslang, $msglang, $ch, $pollyid)
{
    global $DB_dir;
    global $userid;
    global $testcall;

    $url = $DB_dir . "New_Req.php?recid=$recid&effect=$effect&callid=$callid&reqtype=$reqtype&from=$pollyid&phno=$phno&status=$status&syslang=$syslang&msglang=$msglang&testcall=$testcall&ch=$ch";
    $result = doCurl($url);
    return $result;
}

function markCallEndTime($callid)
{
    global $Surveypath;

    $url = $Surveypath . "Update_Call_Endtime.php?callid=$callid";
    $result = doCurl($url);
    return $result;
}

function updateCallStatus($callid, $status)
{
    global $Surveypath;

    $url = $Surveypath . "Update_Call_Status.php?callid=$callid&status=$status";
    $result = doCurl($url);
    return $result;
}

function updateRequestStatus($reqid, $status)
{
    global $Surveypath;
    global $channel;

    $url = $Surveypath . "Update_Request_Status.php?reqid=$reqid&status=$status&ch=$channel";
    $result = doCurl($url);
    return $result;
}

function updateWaitingDlvRequests($id)
{
    global $DB_dir;

    $url = $DB_dir . "Update_Waiting_DLV_Reqs.php?rcallid=$id";
    $result = doCurl($url);
    return $result;
}

function phoneNumBeforeAndAfterConditioning($before, $after, $type, $sender)
{
    global $callid;
    global $Surveypath;

    $url = $Surveypath."Update_Conditioned_PhNo.php?callid=$callid&uncond=$before&cond=$after&type=$type&sender=$sender";
    $result = doCurl($url);
    return $result;
}

// -------------------------------s
// functions to encode, decode, store phone numbers
function PhToKeyAndStore($phno, $sender)
{
    global $DB_dir;

 
    $url = $DB_dir . "insertNewPh.php?sender=$sender&ph=" . $phno;

    $result = doCurl($url);

    return $result;
}


function PhToWorkerKeyAndStore($phno)
{
    global $Surveypath;

    $url = $Surveypath . "insertNewWorkerPh.php?ph=" . $phno;

    $result = doCurl($url);

    return $result;
}

function PhToKey($ph)
{
    global $DB_dir;

    $url = $DB_dir . "phToKey.php?ph=$ph";
    $result = doCurl($url);
    return $result;
}

function KeyToPh($key)
{
    global $Surveypath;

    $url = $Surveypath . "keyToPh.php?key=$key";
    $result = doCurl($url);
    return $result;
}

// Phone Directory Functions
function getPhDir()
{
    global $userid;
    global $DB_dir;

    $url = $DB_dir . "getPhDir.php?user=$userid";
    $result = doCurl($url);
    return $result;
}

function updatePhDir($phoneNumber)
{
    global $userid;
    global $DB_dir;

    $url = $DB_dir . "updatePhDir.php?user=$userid&friend=$phoneNumber";
    $result = doCurl($url);
    return $result;
}

// ////////////////////////////////////////////////////////////////////////////////////
// /////////////////////////// Misc. Functions ////////////////////////////////////////
// ////////////////////////////////////////////////////////////////////////////////////

function createLogFile($id)
{
    global $logFilePath;

    $logFilePathLocal = $logFilePath . ($id%1000);
    if(!file_exists($logFilePathLocal)){
        mkdir($logFilePathLocal);
    }
    $logFile = $logFilePathLocal."\\".$id.".txt";

    $handle = fopen($logFile, 'a');
    return $handle;
}

function sendLogs()
{
    global $DB_dir;
    global $callid;
    global $logEntry;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " called.");

    $LogScript = $DB_dir . "createLogs.php";
    $arr = explode('^', chunk_split($logEntry, 1000, '^')); // Send logs in chunks of 1000 characters
    $i = 0;
    $len = count($arr);

    while ($i < $len) {

        $datatopost = array(
            "callid" => $callid,
            "data" => $arr[$i]
        );

        $ch = curl_init($LogScript);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datatopost);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $returndata = curl_exec($ch);
        $i++;
    }
}

function conditionPhNo($phno, $type)
{
    $returnNumber = $phno;
    if ($type == "SSSeed-Call" || $type == "SSCall-me-back" || $type == "SSCall-in") {

        if (substr($returnNumber, 0, 2) == '92') {
            $returnNumber = substr($returnNumber, 3, strlen($returnNumber) - 2);
            $returnNumber = '0' + $returnNumber;
        }
        elseif (substr($returnNumber, 0, 3) == '+92' || substr($returnNumber, 0, 3) == '092') {
            $returnNumber = substr($returnNumber, 3, strlen($returnNumber) - 3);
            $returnNumber = '0' + $returnNumber;
        }
    }

    return $returnNumber;
}



function doCurl($url)
{

    if ($GLOBALS['callid'] != "")
    {
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is called.");
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    $result = curl_exec($ch);
    curl_close($ch);

    if ($GLOBALS['callid'] != "")
    {
       writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " called with url: $url, is returning: $result");
    }

    return $result;
}


function getFilePath($fileName, $pathOnly = "FALSE")
{

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is called.");

    $fname = explode('.', $fileName);
    $FilePath = ($fname[0] - ($fname[0] % 1000)); // rounding down to the nearest 1000
    $File = $FilePath . "/" . $fileName;

    if ($pathOnly == "TRUE") {
        $File = $FilePath . "/";
    }

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " called with params: $fileName, $pathOnly, is returning: $File");
    return $File;
}

function createFilePath($filePath, $fileName, $pathOnly = "FALSE")
{

    $fname = explode('.', $fileName);

    $FilePathNew = $filePath . ($fname[0] - ($fname[0] % 1000)); // rounding down to the nearest 1000
    if (is_dir($FilePathNew) === false) {
        mkdir($FilePathNew);
    }

    $File = $FilePathNew . "\\" . $fileName;
    if ($pathOnly == "TRUE") {
        $File = $FilePathNew . "\\";
    }

    return $File;
}

// //////////////////////////////////////////// Free Switch lib //////////////////////

function event_socket_create($host, $port, $password)
{
    global $fp;

    $fp = fsockopen($host, $port, $errno, $errdesc) or die("Connection to $host failed");
    socket_set_blocking($fp, false);

    if ($fp) {
        while (!feof($fp)) {
            $buffer = fgets($fp, 1024);
            usleep(50); //allow time for reponse
            if (trim($buffer) == "Content-Type: auth/request") {
                fputs($fp, "auth $password\n\n");
                break;
            }
        }
        return $fp;
    }
    else {
        return false;
    }
}

function event_socket_request($fp, $cmd)
{

    if ($fp) {
        fputs($fp, $cmd . "\n\n");
        usleep(50); //allow time for reponse
        $response = "";
        $i = 0;
        $contentlength = 0;

        while (!feof($fp)) {
            $buffer = fgets($fp, 4096);

            if ($contentlength > 0) {
                $response.= $buffer;
            }

            if ($contentlength === 0) { //if contentlenght is already don't process again
                if (strlen(trim($buffer)) > 0) { //run only if buffer has content
                    $temparray = explode(":", trim($buffer));
                    if ($temparray[0] == "Content-Length") {
                        $contentlength = trim($temparray[1]);
                    }
                }
            }

            usleep(50); //allow time for reponse
            // optional because of script timeout //don't let while loop become endless
            if ($i > 100000) {
                break;
            }
            if ($contentlength > 0) { //is contentlength set
                // stop reading if all content has been read.
                if (strlen($response) >= $contentlength) {
                    if ($i > 0) {
                        $buffer_protoect_limits = fgets($fp, 4096);
                    }

                    break;
                }
            }
            $i++;
        }
        return $response;
    }

    else {
        echo "no handle";
    }
}

function hangupFT()
{
    global $FreeSwitch;
    global $uuid;
    global $fp;

    if ($FreeSwitch == "false") {
        hangup();
    }
    else {
        $cmd = "api lua hangup.lua " . $uuid;
        $response = event_socket_request($fp, $cmd);
        fclose($fp);
    }
}

function makechoices($choices)
{

    $fchoices = "";// types seen "[1 DIGITS], *" , "[1 DIGITS]" , "sometext(1,sometext)"

    if ($choices[0] == '[') {
        $pchoices = explode(",", $choices);// tokenizing all possible choices

        $i = 1;
        while ($pchoices[0][$i] != ' ' && $i < strlen($pchoices[0])) {
            $fchoices.= $pchoices[0][$i];
            ++$i;
        }

        if (strlen($fchoices) > 1) {
            $fchoices = str_replace("-", "", $fchoices);
        }

        $j = 1;
        while ($j < count($pchoices)) {
            $fchoices.= trim($pchoices[$j]);
            ++$j;
        }
    }
    else {
        $i = 0;

        while ($i < strlen($choices)) {
            $j = $i;

            $cchoices = ""; //choice in context
            while ($choices[$j] != '(') {
                $j++;
            }

            $j++;
            while ($choices[$j] != ')') {
                $cchoices.= $choices[$j];
                $j++;
            }

            $j++;
            $i = $j;
            $pchoices = explode(",", $cchoices);
            $fchoices.= $pchoices[0];
        }
    }

    return $fchoices;
}

function makeValidINput($choices, $fchoices) // to make regex for valid input freeswitch like [1 digits], * => \\d+ or 1234* => [1234]
{
    if ($choices[0] == '[') {
        return "d";
    }
    else {
        $valid = '[';
        $i = 0;

        while ($i < strlen($fchoices)) {
            if ($fchoices[$i] != '*' && $fchoices[$i] != '#') {
                $valid.= $fchoices[$i];
            }
            $i++;
        }
        $valid.= ']';
        return $valid;
    }
}

function makeTerminators($fchoices, $terms)  // making terms -- // fchoices choice for freeswitch
{
    $termsin = "";
    $i = 0;
    $b = 0;

    while ($i < strlen($fchoices)) {
        if ($fchoices[$i] == '*' || $fchoices[$i] == '#') {
            $b = 1;
            $termsin.= $fchoices[$i];
        }
        $i++;
    }

    if ($terms == "@" && $b == 1) {
        return $termsin;
    }
    else {
        $terms.= $termsin;
        return $terms;
    }
}

function mapChoice($choices, $fchoice) // fchoice return by freeswitch map corrosponding class, 1 for notify
{
    if ($choices[0] == '[') {
        return $fchoice;
    }
    else {
        $i = 0;

        while ($i < strlen($choices)) {
            $j = $i;

            $cchoices = ""; //choice in context
            while ($choices[$j] != '(') {
                $j++;
            }

            $j++;
            while ($choices[$j] != ')') {
                $cchoices.= $choices[$j];
                $j++;
            }

            $j++;
            $i = $j;
            $pchoices = explode(",", $cchoices);
            if ($fchoice == $pchoices[0]) {
                return $pchoices[1];
            }
        }
        return $fchoice;
    }
}

function calculateMaxDigits($choices, $fchoice)
{

    if ($choices[0] == "[") {
        $i = strpos($choices, '-');

        if ($i !== false) {
            $subchoice = ""; //the part 9-14 in [9-14 digits]
            $i = 1;

            while ($choices[$i] == "-" || is_numeric($choices[$i])) {
                $subchoice.= $choices[$i];
                $i++;
            }

            $subchoice = explode("-", $subchoice);
            $max = (int)($subchoice[1]);
        }
        else {
            $subchoice = ""; //the part 9 in [9 digits]
            $i = 1;

            while (is_numeric($choices[$i])) {
                $subchoice.= $choices[$i];
                $i++;
            }

            $max = (int)($subchoice);
        }

        return $max;
    }
    else {
        return 1; //in these kind of choices like notify(1,notify),donotnotify(2,donotnotify),*(*,*) at a time input numbers require is 1 always
    }
}

function calculateMinDigits($choices, $fchoice)
{
    if ($choices[0] == "[") {
        $i = strpos($choices, '-');

        if ($i !== false) {
            $subchoice = ""; //the part 9-14 in [9-14 digits]
            $i = 1;

            while ($choices[$i] == "-" || is_numeric($choices[$i])) {
                $subchoice.= $choices[$i];
                $i++;
            }
            $subchoice = explode("-", $subchoice);
            $min = (int)($subchoice[0]);
        }
        else {
            $subchoice = ""; //the part 9 in [9 digits]
            $i = 1;

            while (is_numeric($choices[$i])) {
                $subchoice.= $choices[$i];
                $i++;
            }
            $min = (int)($subchoice);
        }
        return $min;
    }
    else {
        return 1; //in these kind of choices like notify(1,notify),donotnotify(2,donotnotify),*(*,*) at a time input numbers require is 1 always
    }
}

function invalid($onBadCoice) // make sure to make proper changes before integrating it to poly -- // to handle if invalid key is entered for freeswitch --
{
    global $Polly_prompts_dir;
    global $BadChoiceKeys;

    if ($onBadCoice == "keysbadChoiceFCN") {
        return $Polly_prompts_dir . "Wrongbutton.wav";
    }
}

function onTimeOut($onTimeout) // to handle if timeout occured for freeswitch
{
    global $Polly_prompts_dir;
    global $TimeOutKeys;

    if ($onTimeout == "keystimeOutFCN") {
        return $Polly_prompts_dir . "Nobutton.wav";
    }
}

function gatherInputTropo($toBeSaid, $params)
{
    $repeat = "TRUE";

    while ($repeat == "TRUE") {
        $repeat = "FALSE";
        $result = ask($toBeSaid, $params);

        if ($result->value == "*") { // pause the system
            ask("", array(
                "choices" => "[1 DIGITS], *",
                "mode" => 'dtmf',
                "repeat" => 1,
                "bargein" => true,
                "timeout" => 300,
                "onHangup" => create_function("$event", "Prehangup()")
            ));
            $repeat = "TRUE";
        }
    }
    return $result;
}

function gatherTnputFreeSwitch($toBeSaid, $invalidFS, $mindigitsFS, $maxdigitsFS, $maxattemptsFS, $timeoutFS, $bargein, $termFS, $validInput, $onTimeOutFS, $interdigitTimeout)
{
    global $uuid;
    global $fp;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " entered.");
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", " **** gatherInputFreeSwitch Parameters **** ");
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "toBeSaid : ".$toBeSaid);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "invalidFS : ".$invalidFS);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "mindigitsFS : ".$mindigitsFS);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "maxdigitsFS : ".$maxdigitsFS);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "maxattemptsFS : ".$maxattemptsFS);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "timeoutFS : ".$timeoutFS);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "bargein : ".$bargein);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "termFS : ".$termFS);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "validInput : ".$validInput);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "onTimeOutFS : ".$onTimeOutFS);
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "interdigitTimeout : ".$interdigitTimeout);

    $output = (object)array(
        'name' => 'choice',
        'value' => ''
    );
    $repeat = "TRUE";
    $kaho = "file_string://";
    $s = preg_split('/[ \n]/', $toBeSaid);

    for ($i = 0; $i < count($s); $i++) {
        $j = 0;

        if ($s[0] == "") {
            $j = 1;
        }

        if ($i > $j) {
            $kaho.= "!" . $s[$i];
        }
        else {
            $kaho.= $s[$i];
        }
    }

    while ($repeat == "TRUE") {
        $repeat = "FALSE";
        $cmd = "api lua askGather.lua " . $uuid . " " . $kaho . " " . $invalidFS . " " . $mindigitsFS . " " . $maxdigitsFS . " " . $maxattemptsFS . " " . $timeoutFS . " " . $termFS . " " . $validInput . " " . $onTimeOutFS . " " . $interdigitTimeout;
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "FreeSwitch called to execute command : ".$cmd);
        $response = event_socket_request($fp, $cmd);


        if (substr($response, 1)) {
            $val = substr($response, 1);

            if ($val[0] == ' ' || $val[0] == '-') {
                $output->value = $val[1];
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Response of " . __FUNCTION__ . " : " . $val);
            }
            elseif ($val[0] == '_') {
                $i = 1;

                while ($i < strlen($val)) {
                    $output->value.= $val[$i];
                    $i++;
                }
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Response of " . __FUNCTION__ . " : " . $val);
            }
            else {
                $output->name = "not_Good_timeout_or_invalid";
                $output->value = "-";
            }
        }
        else {
            $output->name = "not_Good_timeout_or_invalid";
            $output->value = "-";
        }
    }

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " returning.");
    isThisCallActive();
    return $output;
}

function gatherInput($toBeSaid, $params)
{
    global $Polly_prompts_dir;
    global $Silence;
    global $FreeSwitch;

    if ($FreeSwitch == "false") {
        return gatherInputTropo($toBeSaid, $params);
    }
    else {
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called with prompt: " . $toBeSaid . " and parameters: " . implode(', ', $params));
        if (isThisCallActive() == "true") {
            // making parameters
            //parameters that should always be in ask array no matter what..choices...bargein..timeout
            $choices = $params['choices']; //choices given by user for tropo
            $fchoices = makechoices($choices); //fchoices for freeswitch in format 123 or 1234* or 1*#..//fchoices made out of choices given by user,for freeswitch
            $mindigitsFS = calculateMinDigits($choices, $fchoices);
            $maxdigitsFS = calculateMaxDigits($choices, $fchoices);
            $bargein = $params['bargein'];
            $timeoutFS = $params['timeout'] * 1000; //to convert to millisecs as freeswitch timeout is in millisecs
            $validInput = makeValidINput($choices, $fchoices);

            // parameters that should may be in ask array..repeat || attempt...terminator..onBadChoice..onTimeout
            $termFS = "@"; //default value menas no terminator
            if (checkifexists('terminator', $params) == true) $termFS = $params['terminator']; //intialized with passed parameter terminator
            $termFS = makeTerminators($fchoices, $termFS); //making it for freeswitch like if * is not in term but in choice it will put it in terminator

            $maxattemptsFS = 0; //default value
            if (checkifexists('attempts', $params) == true) $maxattemptsFS = $params['attempts']; //intialized with passed parameter attempts
            if (checkifexists('repeat', $params) == true) $maxattemptsFS = $params['repeat'] + 1; //intialized with passed parameter repeat

            $invalidFS = $Polly_prompts_dir . $Silence; //default value that is silence
            if (checkifexists('onBadChoice', $params) == true) $invalidFS = invalid($params['onBadChoice']); //intialized with prompt corrosponding to the onBadChoice value

            $onTimeOutFS = "-"; //default value that is silence
            if (checkifexists('onTimeout', $params) == true) $onTimeOutFS = onTimeOut($params['onTimeout']); //intialized with prompt corrosponding to the onTimeout value

            $interdigitTimeout=$timeoutFS;//default value is equal to timeout in freeswitch 
            if (checkifexists('interdigitTimeout', $params) == true) $interdigitTimeout = $params['interdigitTimeout'] * 1000; //intialized with passed parameter interdigitTimeout

            $result = gatherTnputFreeSwitch($toBeSaid, $invalidFS, $mindigitsFS, $maxdigitsFS, $maxattemptsFS, $timeoutFS, $bargein, $termFS, $validInput, $onTimeOutFS, $interdigitTimeout);
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " returning.");
            return $result;
        }
    }
}

function checkifexists($parameter, $array) // checking if  parameter exists in array
{

    if (array_key_exists($parameter, $array)) {
        return true;
    }
    return false;
}

function recordAudio($toBeSaid, $params)
{
    global $FreeSwitch;
    global $uuid;
    global $fp;
    global $Feedback_Dir;
    global $CallRecordings_Dir;
    global $scripts_dir;
    global $CallRecordings_Dir_Baang;
    global $UserIntro_Dir_Baang;
    global $Feedback_Dir_Baang;

    $recid = "";
    $result = "";
    $filepathFS = "";

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called with prompt: " . $toBeSaid . " and parameters: " . implode(', ', $params));

    if ($FreeSwitch == "false") {
        $result = record($toBeSaid, $params);
    }
    else {

        if (isThisCallActive() == "true") {

            $silTimeout = 5; // default silence timeout
            if (checkifexists('silenceTimeout', $params) == true) {
                $silTimeout = $params['silenceTimeout'];
            }

            $maxTime = 30; //default maximum time
            if (checkifexists('maxTime', $params) == true) {
                $maxTime = $params['maxTime'];
            }

            $recordURI = '';
            if (checkifexists('recordURI', $params) == true) {
                $recordURI = $params['recordURI'];
            }

            $beep = 0;
            if (checkifexists('beep', $params) == true) {
                $beep = $params['beep'];
            }

            $timeout = 30;
            if (checkifexists('timeout', $params) == true) {
                $timeout = $params['timeout'];
            }

            $bargein = 1;
            if (checkifexists('bargein', $params) == true) {
                $bargein = $params['bargein'];
            }

            $terminator = '';
            if (checkifexists('terminator', $params) == true) {
                $terminator = $params['terminator'];
            }

            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "beep : ".$beep);
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "timeout : ".$timeout);
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "bargein : ".$bargein);
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "silenceTimeout : ".$silenceTimeout);
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "maxTime : ".$maxTime);
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "terminator : ".$terminator);
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "recordURI : ".$recordURI);

            $rec_feed = 0;

            if (strpos($recordURI, 'process_feedback') !== false) {
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "condition met: process_feedback ");
                $parameterarray = explode("&", explode("?", $recordURI) [1]);
                $fbid = explode("=", $parameterarray[0]) [1];

                if (strpos($recordURI, 'baang') !== false) {
                    $filepathFS = $Feedback_Dir_Baang;
                    $userid = explode("=", $parameterarray[2]) [1];
                    $rec_id = explode("=", $parameterarray[1]) [1];
                    $filepathFS = createFilePath($filepathFS, $fbid . ".wav", TRUE);
                    $filepathFS.= "Feedback-" . $fbid . "-u-" . $userid . "-r-" . $rec_id;
                }
                else {
                    $filepathFS = $Feedback_Dir;
                    $filepathFS = createFilePath($filepathFS, $fbid . ".wav", TRUE);
                    $filepathFS.= "Feedback-" . $fbid . "-";
                    $i = strpos($recordURI, '=');
                    $i = $i + 1;
                    $fbid = "";

                    while ($recordURI[$i] != '&') {
                        $fbid.= $recordURI[$i];
                        $i = $i + 1;
                    }

                    while ($recordURI[$i] != '=') {
                        $i = $i + 1;
                    }

                    $i = $i + 1;

                    while ($i < strlen($recordURI)) {
                        $filepathFS.= $recordURI[$i];
                        $i = $i + 1;
                    }
                }

                $filepathFS.= ".wav";
                $rec_feed = 1;
            }
            elseif (strpos($recordURI, 'process_recording') !== false) {
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "condition met: process_recording ");
                $rec_feed = 0;
                $parameterarray = explode("&", explode("?", $recordURI) [1]);
                $recid = explode("=", $parameterarray[0]) [1];

                if (strpos($recordURI, 'name') !== false) {
                    $filepathFS = $UserIntro_Dir_Baang;
                    $filepathFS.= "intro-" . $recid . ".wav";
                    $rec_feed = 6;
                }
                elseif (strpos($recordURI, 'baang') !== false) {
                    $rec_feed = 2;
                    $filepathFS = $CallRecordings_Dir_Baang;
                    $filepathFS = createFilePath($filepathFS, $recid . ".wav", TRUE);
                    $filepathFS.= "Rec-" . $recid . ".wav";
                }
                else {
                    $filepathFS = $CallRecordings_Dir;
                    $filepathFS = createFilePath($filepathFS, $recid . ".wav", TRUE);
                    $filepathFS.= "s-" . $recid . ".wav";
                    $rec_feed = 0;
                }
            }
            elseif (strpos($recordURI, 'process_FriendNamerecording') !== false) {
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "condition met: process_FriendNamerecording ");
                $parameterarray = explode("&", explode("?", $recordURI) [1]);
                $reqid = explode("=", $parameterarray[0]) [1];
                $userid = explode("-", $reqid) [0];
                $filepathFS = $Drive . ":/xampp/htdocs/wa/Praat/FriendNames/";
                $filepathFS = createFilePath($filepathFS, $userid . ".wav", TRUE);
                $rec_feed = 6;
                $filepathFS.= $reqid . ".wav";
            }
            elseif (strpos($recordURI, 'process_UserNamerecording') !== false) {
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "condition met: process_UserNamerecording ");
                $parameterarray = explode("&", explode("?", $recordURI) [1]);
                $callid = explode("=", $parameterarray[0]) [1];
                $filepathFS = $Drive . ":/xampp/htdocs/wa/Praat/UserNames/";
                $filepathFS = createFilePath($filepathFS, $callid . ".wav", TRUE);
                $rec_feed = 6;
                $filepathFS.= "UserName-" . $callid . ".wav";
            }
            elseif(strpos($recordURI, 'insert_question_answer') !== false)
            {
                global $survey_answers;
                $survey_answer_id = doCurl($recordURI);
                $survey_answer_file_name = $survey_answer_id . ".wav";

                $filepathFS = $survey_answers;
                $filepathFS = createFilePath($filepathFS, $survey_answer_file_name, TRUE);
                $rec_feed = 7;
                $filepathFS.= $survey_answer_file_name;
            }
            elseif(strpos($recordURI, 'insert_feedback') !== false)
            {
                global $survey_comments;
                $survey_feedback_id = doCurl($recordURI);
                $survey_comment_file_name = $survey_feedback_id . ".wav";

                $filepathFS = $survey_comments;
                $filepathFS = createFilePath($filepathFS, $survey_comment_file_name, TRUE);
                $rec_feed = 8;
                $filepathFS.= $survey_comment_file_name;
            }
            elseif (strpos($recordURI, 'record_survey') !== false) {
                global $userid;
                global $language_demographic_dir;
                global $profession_demographic_dir;
                global $location_demographic_dir;
                global $disabled_demographic_dir;

                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "condition met: record_survey ");

                if (strpos($recordURI, "type=LANGUAGE") !== false) {
                    $filepathFS = $language_demographic_dir;
                }
                elseif (strpos($recordURI, "type=PROFESSION") !== false) {
                    $filepathFS = $profession_demographic_dir;
                }
                elseif (strpos($recordURI, "type=LOCATION") !== false) {
                    $filepathFS = $location_demographic_dir;
                }
                elseif (strpos($recordURI, "type=DISABLED") !== false) {
                    $filepathFS = $disabled_demographic_dir;
                }

                $filepathFS = createFilePath($filepathFS, $userid . ".wav", TRUE);
                $rec_feed = 6;
                $filepathFS.= $userid . ".wav";
            }

            $kaho = "file_string://";
            $s = preg_split('/[ \n]/', $toBeSaid);

            for ($i = 0; $i < count($s); $i++) {
                $j = 0;

                if ($s[0] == "") {
                    $j = 1;
                }

                if ($i > $j) {
                    $kaho.= "!" . $s[$i];
                }
                else {
                    $kaho.= $s[$i];
                }
            }

            $filepathFS = str_replace("\\", "_", $filepathFS);
            $kaho = rtrim($kaho, "!");
            $cmd = "api lua record.lua " . $uuid . " " . $kaho . " " . $filepathFS . " " . $maxTime . " " . $silTimeout; // some suggest using 500 as the threshold of silence
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "FreeSwitch called to execute command : ".$cmd);
            $result = event_socket_request($fp, $cmd);

            isThisCallActive();
            $filepathFS = str_replace("_", "\\", $filepathFS);
            correctWavFT($filepathFS);

            if ($rec_feed === 0) {
                $res = doCurl($scripts_dir . "processAudFile.php?path=s-$recid" . ".wav");
            }
            elseif ($rec_feed === 2 || $rec_feed === 5 || $rec_feed === 6) {
                $res = doCurl($recordURI);
            }
        }
    }

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " complete. Now returning.");
    return $result;
}

function correctWavFT($filepath)
{
    global $FreeSwitch;

    if ($FreeSwitch == "false") {
    }
    else {
        $rep = file_get_contents($filepath);
        $rep[20] = "\x01";
        $rep[21] = "\x00";
        file_put_contents($filepath, $rep);
    }
}

function askFT($toBeSaid, $choices, $mode, $repeat, $bargein, $timeout, $hanguppr, $mindigitsFS, $maxdigitsFS, $maxattemptsFS, $timeoutFS, $termFS, $invalidFS)
{
    global $FreeSwitch;
    global $uuid;
    global $fp;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "In" . __FUNCTION__);

    $output = (object)array(
        'name' => 'choice',
        'value' => ''
    );

    if ($FreeSwitch == "false") {
        $result = ask($toBeSaid, array(
            "choices" => $choices,
            "mode" => $mode,
            "repeat" => $repeat,
            "bargein" => $bargein,
            "timeout" => $timeout,
            "terminator" => $termFS,
            "onHangup" => $hanguppr
        ));
        $output = $result;
    }
    else {
        $kaho = "file_string://";
        $s = preg_split('/[ \n]/', $toBeSaid);

        for ($i = 0; $i < count($s); $i++) {
            $j = 0;

            if ($s[0] == "") {
                $j = 1;
            }

            if ($i > $j) {
                $kaho.= "!" . $s[$i];
            }
            else {
                $kaho.= $s[$i];
            }
        }

        $kaho = rtrim($kaho, "!");
        $cmd = "api lua ask.lua " . $uuid . " " . $kaho . " " . $invalidFS . " " . $mindigitsFS . " " . $maxdigitsFS . " " . $maxattemptsFS . " " . $timeoutFS . " " . $termFS;
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "FreeSwitch called to execute command : ".$cmd);
        $response = event_socket_request($fp, $cmd); //here

        if (substr($response, 1)) {

            $val = substr($response, 1);

            if ($val[0] == '_' || $val[0] == '+' || $val[0] == ' ') {
                $output->value = $val[1];
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Response of " . __FUNCTION__ . " : " . $val);
            }
            else {
                $output->name = "not_Good_timeout_or_invalid";
                $output->value = $val[1];
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Response of " . __FUNCTION__ . " : " . $val);
            }
        }
        else {
            $output->name = "not_Good_timeout_or_invalid";
            $output->value = "-";
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Response of " . __FUNCTION__ . " : " );
        }
    }

    isThisCallActive();
    return $output;
}

function playBeep()
{
    global $uuid;
    global $fp;

    if (isThisCallActive() == "true") {
        
        $cmd = "api lua play_beep.lua " . $uuid;
        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "FreeSwitch called to execute command : ".$cmd);
        $response = event_socket_request($fp, $cmd); //here
    }

    isThisCallActive();
}

function sayInt($toBeSaid)
{
    if (isThisCallActive() == "true") {
        $choices = "[1 DIGITS], *, #";
        $mode = 'dtmf';
        $repeatMode = 0;
        $bargein = true;
        $timeout = 0.1;
        $hanguppr = "Prehangup";
        $mindigitsFS = 1;
        $maxdigitsFS = 1;
        $maxattemptsFS = 1;
        $timeoutFS = 100;
        $termFS = "*#";
        $invalidFS = "nothing";
        $repeat = "TRUE";

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " about to play: " . $toBeSaid . " using askFT function");

        while ($repeat == "TRUE") {
            $repeat = "FALSE";
            $result = askFT($toBeSaid, $choices, $mode, $repeatMode, $bargein, $timeout, $hanguppr, $mindigitsFS, $maxdigitsFS, $maxattemptsFS, $timeoutFS, $termFS, $invalidFS);

            if ($result->name == 'choice') {
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " prompt " . $toBeSaid . " was barged-in with " . ($result->value));
            }
        }

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " complete. Now returning: value: " . ($result->value) . ", name: " . ($result->name));
        return $result;
    }
}

//////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// End of Code ////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
?>