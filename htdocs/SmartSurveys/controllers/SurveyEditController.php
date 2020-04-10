<?php

/**
 * The SurveyEditController class is a Controller that allows a user to enter a survey
 * or make changes to an existing survey
 *
 */
class SurveyEditController extends Controller
{
    /**
     * Handle the page request
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleRequest(&$request)
    {
        $user = $this->getUserSession();
        $this->assign('user', $user);

        if (isset($request['action']))
            $this->handleAction($request);

        $survey = $this->getSurvey($request);
        $this->assign('survey', $survey);

        if (isset($request['status']) && $request['status'] == 'success')
        {
            date_default_timezone_set('Asia/Dubai');
            $date = date_create(); 
            $this->assign('statusMessage', 'Survey updated successfully @'. date_format($date, ' Y-m-d H:i:s') );
        }
            
    }

    /**
     * Handle a user submitted action
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleAction(&$request)
    {
        switch ($request['action'])
        {
            case 'get_survey':
                $this->getSurvey($request['survey_id']);
                echo "in get survey properties";
                break;

            case 'edit_survey':
                $this->editSurvey($request);
                break;

            case 'save_continue':
                $this->editandContinue($request);
                break;

            case 'delete_survey':
                $this->deleteSurvey($request);
                break;
        }
    }

    /**
     * Query the database for a survey_id or create an empty survey
     *
     * @param array $request the page parameters from a form post or query string
     * @return Survey $survey returns a Survey object
     * @throws Exception throws exception if survey id is not found
     */
    protected function getSurvey(&$request)
    {
        if (!empty($request['survey_id']))
        {
            $survey = Survey::queryRecordById($this->pdo, $request['survey_id']);
            if (! $survey)
                throw new Exception("Survey ID not found in database");

            // Keep track of existing ids so that any records not updated are deleted
            $survey->existing_question_ids = array();
            $survey->existing_choice_ids = array();         
            $survey->question_choice_pair = $survey->getRadioQuestionsChoicePairforDependency($this->pdo);

            $survey->getQuestions($this->pdo);
            foreach ($survey->questions as $question)
            {
                $survey->existing_question_ids[] = $question->question_id;
                $question->getChoices($this->pdo);

                foreach ($question->choices as $choice)
                    $survey->existing_choice_ids[] = $choice->choice_id;
            }
        }
        else
        {
            $survey = new Survey;
            $survey->questions = array();
            $survey->file_name_description = '';
            $survey->file_name_instructions = '';
            $survey->survey_description = '';
            $survey->survey_instructions = '';
            $survey->time_created = '0000-00-00 00:00:00';
            $survey->time_modified = '0000-00-00 00:00:00';
            $survey->no_of_respondents = '5';
            $survey->min_reward_per_response = '50';
            $survey->max_reward_per_response = '50';
            
            $user = $_SESSION['login'];
            $survey->requester_id = $user->requester_id;

            // Create 1 empty question
            $question = new Question;
            $question->question_type = 'radio';
            $question->choices = array();

            // Create 1 empty choice
            $choice = new Choice;
            $question->choices[] = $choice;

            $survey->questions[] = $question;
        }

        return $survey;
    }

    /**
     * Set the values for the survey object based on form parameters
     *
     * @param Survey $survey the survey object to update
     * @param array $request the page parameters from a form post or query string
     */
    protected function setSurveyValues(Survey $survey, &$request)
    {

        $old_survey = clone $survey;
        
        $survey->updateValues($request);

        $this->setSurveyQuestions($survey, $request);

        if (!empty($old_survey))
        {
            $survey->file_name_description = $old_survey->file_name_description;
            $survey->file_name_instructions = $old_survey->file_name_instructions;
            
            for ($i = 0; $i < sizeof($old_survey->questions); $i++) 
            {
                //$survey->questions[$i]->file_name = $old_survey->questions[$i]->file_name;

                for ($j = 0; $j < sizeof($survey->questions); $j++)
                {
                    if ($survey->questions[$j]->question_id == $old_survey->questions[$i]->question_id)
                    {
                        $survey->questions[$j]->file_name = $old_survey->questions[$i]->file_name;
                        if(in_array($survey->questions[$j]->question_type, array('radio')) && ($survey->questions[$j]->question_type == $old_survey->questions[$i]->question_type))
                        {
                            for ( $k=0; $k< sizeof($old_survey->questions[$i]->choices); $k++)
                            {
                                for ($l=0; $l< sizeof($survey->questions[$j]->choices); $l++)
                                {
                                    if($survey->questions[$j]->choices[$l]->choice_id == $old_survey->questions[$i]->choices[$k]->choice_id)
                                    {
                                        $survey->questions[$j]->choices[$l]->file_name = $old_survey->questions[$i]->choices[$k]->file_name;
                                    }
                                }
                            }
                        }
                        
                    }
                }
                
            } 
        }
 
    }

    /**
     * Set the survey's questions based on form parameters
     *
     * @param Survey $survey the survey object to update
     * @param array $request the page parameters from a form post or query string
     */
    protected function setSurveyQuestions(Survey $survey, &$request)
    {
        $survey->questions = array();
        $questionOrder = 1;
        if (!empty($request['question_type']))
        {            
            foreach ($request['question_type'] as $questionID => $questionType)
            {
                $question = new Question;
                if (is_numeric($questionID))
                    $question->question_id = $questionID;
                $question->question_type = $questionType;

                $question->question_text = $request['question_text'][$questionID];
                if(isset($request['dependent'][$questionID]))
                {
                    $question->dependent = 1;

                    $question_choice_pair_tokens = $request['dependent_question_choice_pair'][$questionID];
                    $question_choice_pair_tokens = explode('_',$question_choice_pair_tokens,4);
                    $question->dependent_question_id = intval($question_choice_pair_tokens[0]);
                    $question->dependent_choice_id = intval($question_choice_pair_tokens[2]);
                }
                else
                {
                    $question->dependent = 0;
                    $question->dependent_question_id = 0;
                    $question->dependent_choice_id = 0;
                }
               
                $question->generic = isset($request['generic'][$questionID]) ? 1 : 0;
                $question->file_name = '';
                if (isset($request['file_updated'][$questionID]))
                {
                    if ($request['file_updated'][$questionID] == '1')
                    {
                        //echo 'about to set file_updated to 1';
                        //echo $request['file_updated'][$questionID];
                        $question->file_updated = 1;
                    }
                    else 
                    {
                        echo 'about to set file_updated to 0';
                         $question->file_updated = 0;
                    }
                }
                else
                {
                    $question->file_updated = 0;
                }
                $question->question_order = $questionOrder++;

                $this->setQuestionChoices($question, $questionID, $request);
                
                $survey->questions[] = $question;
            }

        }

    }

    /**
     * Set the question's choices based on form parameters
     *
     * @param Question $question the question object to update
     * @param array $request the page parameters from a form post or query string
     */
    protected function setQuestionChoices(Question $question, $questionID, &$request)
    {
        if (in_array($question->question_type, array('radio')) && isset($request['choice_text'][$questionID]))
        {
            $question->choices = array();
            $choiceOrder = 1;
            foreach ($request['choice_text'][$questionID] as $choiceID => $choiceText)
            {
                $choice = new Choice;
                if (is_numeric($choiceID))
                    $choice->choice_id = $choiceID;
                $choice->choice_text = $choiceText;
                $choice->file_name = '';
                if (isset($request['choice_file_updated'][$questionID][$choiceID]))
                {
                    if ($request['choice_file_updated'][$questionID][$choiceID] == '1')
                    {

                        $choice->file_updated = 1;
                    }
                    else 
                    {
                         $choice->file_updated = 0;
                    }
                }
                else
                {
                    $choice->file_updated = 0;
                }
                $choice->choice_order = $choiceOrder++;
                $question->choices[] = $choice;
            }
        }
    }

    /**
     * Store the survey, questions and choices in the database
     *
     * @param Survey $survey the survey object to store in the database
     */
    protected function storeSurvey(Survey $survey)
    {
        // Keep track of stored ids so that any records not updated are deleted
        $stored_question_ids = array();
        $stored_choice_ids = array();

        $survey->storeRecord($this->pdo);

        foreach ($survey->questions as $question)
        {
            $question->survey_id = $survey->survey_id;
            $question->storeRecord($this->pdo);
            $stored_question_ids[] = $question->question_id;

            foreach ($question->choices as $choice)
            {
                $choice->question_id = $question->question_id;
                $choice->storeRecord($this->pdo);
                $stored_choice_ids[] = $choice->choice_id;
            }
        }

        // Delete choices that were removed
        if (!empty($survey->existing_choice_ids))
        {
            $deleted_choice_ids = array_diff($survey->existing_choice_ids, $stored_choice_ids);
            Choice::deleteChoices($this->pdo, $deleted_choice_ids);
        }

        // Delete questions that were removed
        if (!empty($survey->existing_question_ids))
        {
            $deleted_question_ids = array_diff($survey->existing_question_ids, $stored_question_ids);
            Question::deleteQuestions($this->pdo, $deleted_question_ids);
        }

    }

    /**
     * Store the survey, questions and choices in the database
     *
     * @param Survey $survey the survey object to store in the database
     */
    protected function storeSurveyRemoveDeletedQuestionsChoicesRecordings(Survey $survey)
    {
        // Keep track of stored ids so that any records not updated are deleted
        $stored_question_ids = array();
        $stored_choice_ids = array();

        $survey->storeRecord($this->pdo);

        foreach ($survey->questions as $question)
        {
            $question->survey_id = $survey->survey_id;
            $question->storeRecord($this->pdo);
            $stored_question_ids[] = $question->question_id;

            foreach ($question->choices as $choice)
            {
                $choice->question_id = $question->question_id;
                $choice->storeRecord($this->pdo);
                $stored_choice_ids[] = $choice->choice_id;
            }
        }

        $deleted_choice_ids = array();
        $deleted_question_ids = array();
        // Delete choices that were removed
        if (!empty($survey->existing_choice_ids))
        {
            $deleted_choice_ids = array_diff($survey->existing_choice_ids, $stored_choice_ids);
            Choice::deleteChoices($this->pdo, $deleted_choice_ids);
        }
        
        // Delete questions that were removed
        if (!empty($survey->existing_question_ids))
        {
            //echo '<pre>'; print_r($survey->existing_question_ids); echo '</pre>';
            //echo '<pre>'; print_r($stored_question_ids); echo '</pre>';
            $deleted_question_ids = array_diff($survey->existing_question_ids, $stored_question_ids);
            
            Question::deleteQuestions($this->pdo, $deleted_question_ids);
        }


        $dir = "recordings/";
        //echo $deleted_question_ids;
        //echo "<br>";
        // Open a directory, and read its contents
        if (is_dir($dir)){
          if ($dh = opendir($dir)){
            while (($file = readdir($dh)) !== false){
            if (strpos($file, '.wav') !== false) {

                if(!empty($deleted_question_ids))
                    {
                        foreach($deleted_question_ids as $del_val)
                        {
                            $delfile = "Question_" . strval($del_val)."_";
                            //echo $delfile . "<br>";
                            if (strpos($file, $delfile) !== false) 
                            {
                                //echo "Deletedfilename:" . $file . "<br>";
                                if (file_exists($dir.$file))
                                {
                                    $deleted= unlink($dir.$file);

                                    if ($deleted)
                                    {
                                        echo "The file has been successfully deleted";
                                    }
                                    else
                                    {
                                        echo "The file has not been successfully deleted";
                                    }
                                }
                                else
                                {
                                        echo "The original file that you want to delete doesn't exist";
                                }
                            }
                        }
                    }


                if(!empty($deleted_choice_ids))
                    {
                        foreach($deleted_choice_ids as $del_val)
                        {
                            $delfile = "Choice_" . strval($del_val)."_";
                            //echo $delfile . "<br>";
                            if (strpos($file, $delfile) !== false) 
                            {
                                //echo "Deletedfilename:" . $file . "<br>";
                                if (file_exists($dir.$file))
                                {
                                    $deleted= unlink($dir.$file);

                                    if ($deleted)
                                    {
                                        echo "The file has been successfully deleted";
                                    }
                                    else
                                    {
                                        echo "The file has not been successfully deleted";
                                    }
                                }
                                else
                                {
                                        echo "The original file that you want to delete doesn't exist";
                                }
                            }
                        }
                    }


                    //echo "filename:" . $file . "<br>";
                }
            }
            closedir($dh);
          }
        }

    }

    /**
     * delete the recording files if file exist
     *
     * @param $filepath file path to delete recording file
     */
    protected function deleteRecordingFile($filepath)
    {
        if (file_exists($filepath))
        {
            $deleted= unlink($filepath);

            if ($deleted)
            {
                echo "The file has been successfully deleted";
            }
            else
            {
                echo "The file has not been successfully deleted";
            }
        }
        else
        {
            echo "The original file that you want to delete doesn't exist";
        }
    }

    /**
     * Store the recording files of survey, questions and choices in the recordings directory in project root folder
     *
     * @param Survey $survey the survey object to store its recording file
     */
    protected function saveRecordings(Survey $survey)
    {

        
        $sid = strval($survey->survey_id);
        //choice_recording_file
        //question_recording_file
        $errors = [];
        $path = 'recordings/';
        $extensions = ['wav', 'mp3'];

        if (!empty($_FILES))
        {
            //echo "Following Files have been uploaded";
            //echo "<pre>".print_r($_FILES,true)."</pre>";

            if (isset($_FILES['survey_description']))
            {

                if (!empty($_FILES['survey_description']['name']))
                {
                    $errors = [];

                    $survey->file_name_description = $_FILES['survey_description']['name'];
                    $sdfile_name = "Survey_Description_".$sid."_".$_FILES['survey_description']['name'];
                    $sdfile_tmp = $_FILES['survey_description']['tmp_name'];
                    $sdfile_type = $_FILES['survey_description']['type'];
                    $sdfile_size = $_FILES['survey_description']['size'];

                    $sdarr = explode(".", $_FILES['survey_description']['name']);
                    $sdfile_ext = strtolower(array_pop($sdarr));

                    //$sdfile_ext = strtolower(end(explode('.', $_FILES['survey_description']['name'])));
                    $sdfile = $path . $sdfile_name;

                    if (!in_array($sdfile_ext, $extensions)) {
                        $errors[] = 'Extension not allowed: ' . $_FILES['survey_description']['name'] . ' ' . $sdfile_type;
                    }

                    if ($sdfile_size > 2097152) {
                        $errors[] = 'File size exceeds limit: ' . $_FILES['survey_description']['name'] . ' ' . $sdfile_type;
                    }

                    if (empty($errors)) {
                        move_uploaded_file($sdfile_tmp, $sdfile);
                    }
                    if ($errors) print_r($errors);

                }
                else
                {
                    //echo "Error: Survey Description files was not uploaded";
                }
            }

            if (isset($_FILES['survey_instructions']))
            {

                if (!empty($_FILES['survey_instructions']['name']))
                {
                    $errors = [];

                    $survey->file_name_instructions = $_FILES['survey_instructions']['name'];
                    $sifile_name = "Survey_Instructions_".$sid."_".$_FILES['survey_instructions']['name'];
                    $sifile_tmp = $_FILES['survey_instructions']['tmp_name'];
                    $sifile_type = $_FILES['survey_instructions']['type'];
                    $sifile_size = $_FILES['survey_instructions']['size'];

                    $siarr = explode(".", $_FILES['survey_instructions']['name']);
                    $sifile_ext = strtolower(array_pop($siarr));

                    //$sifile_ext = strtolower(end(explode('.', $_FILES['survey_instructions']['name'])));
                    $sifile = $path . $sifile_name;

                    if (!in_array($sifile_ext, $extensions)) {
                        $errors[] = 'Extension not allowed: ' . $_FILES['survey_instructions']['name'] . ' ' . $sifile_type;
                    }

                    if ($sifile_size > 2097152) {
                        $errors[] = 'File size exceeds limit: ' . $_FILES['survey_instructions']['name'] . ' ' . $sifile_type;
                    }

                    if (empty($errors)) {
                        move_uploaded_file($sifile_tmp, $sifile);
                    }
                    if ($errors) print_r($errors);

                }
                else
                {
                    //echo "Error: Survey Instruction files was not uploaded";
                }
            }

            if (isset($_FILES['question_recording_file']) || isset($_FILES['choice_recording_file']))                
            {
                $errors = [];
                $qno = 0;
                $cno = 0;
                foreach ($survey->questions as $question)
                { 
                    
                    if (isset($_FILES['question_recording_file']) && count($_FILES['question_recording_file']['tmp_name'])>0)
                    {
                        if ($question->file_updated > 0)
                        {
                            
                            $question->file_updated = 0;

                            $qid = strval($question->question_id);
                            $qfile_name = "Question_".$qid."_".$_FILES['question_recording_file']['name'][$qno];
                            $qfile_tmp = $_FILES['question_recording_file']['tmp_name'][$qno];
                            $qfile_type = $_FILES['question_recording_file']['type'][$qno];
                            $qfile_size = $_FILES['question_recording_file']['size'][$qno];
                            
                            $qarr = explode(".", $_FILES['question_recording_file']['name'][$qno]);
                            $qfile_ext = strtolower(array_pop($qarr));

                            //$qfile_ext = strtolower(end(explode('.', $_FILES['question_recording_file']['name'][$qno])));


                            $qfile = $path . $qfile_name;

                            if (!in_array($qfile_ext, $extensions)) {
                                $errors[] = 'Extension not allowed: ' . $_FILES['question_recording_file']['name'][$qno] . ' ' . $qfile_type;
                            }

                            if ($qfile_size > 2097152) {
                                $errors[] = 'File size exceeds limit: ' . $_FILES['question_recording_file']['name'][$qno] . ' ' . $qfile_type;
                            }

                            if (empty($errors)) {
                                
                                move_uploaded_file($qfile_tmp, $qfile);

                                if ($question->file_name != '')
                                {
                                    $old_qfile_name = "Question_".$qid."_".$question->file_name;
                                    $this->deleteRecordingFile($path . $old_qfile_name);
                                }

                                $question->file_name = $_FILES['question_recording_file']['name'][$qno];
                            }
                            if ($errors) print_r($errors);
                        }
                    }
                    //upload choices files
                    
                    foreach ($question->choices as $choice)
                    {
                        
                        if (isset($_FILES['choice_recording_file']) && count($_FILES['choice_recording_file']['tmp_name'])>0)
                        {
                            if ($choice->file_updated > 0)
                            {

                                $choice->file_updated = 0;
                                /*
                                if ($choice->file_name == '')
                                {
                                    $choice->file_name = $_FILES['choice_recording_file']['name'][$cno];
                                }
                                */
                                
                              	while (($_FILES['choice_recording_file']['size'][$cno]) < 1)
                                {
                                    $cno = $cno + 1; 
                                }

                                if($cno == count($_FILES['choice_recording_file']['tmp_name']))
                                {
                                    break;
                                }

                                $cid = strval($choice->choice_id);
                                $cfile_name = "Choice_".$cid."_".$_FILES['choice_recording_file']['name'][$cno];
                                $cfile_tmp = $_FILES['choice_recording_file']['tmp_name'][$cno];
                                $cfile_type = $_FILES['choice_recording_file']['type'][$cno];
                                $cfile_size = $_FILES['choice_recording_file']['size'][$cno];
                                
                                $carr = explode(".", $_FILES['choice_recording_file']['name'][$cno]);
                                $cfile_ext = strtolower(array_pop($carr));

                                $cfile = $path . $cfile_name;
                                
                                if (!in_array($cfile_ext, $extensions)) {
                                    $errors[] = 'Extension not allowed: ' . $_FILES['question_recording_file']['name'][$cno] . ' ' . $cfile_type;
                                }

                                if ($cfile_size > 2097152) {
                                    $errors[] = 'File size exceeds limit: ' . $_FILES['question_recording_file']['name'][$cno] . ' ' . $cfile_type;
                                }

                                

                                if (empty($errors)) {

                                    move_uploaded_file($cfile_tmp, $cfile);

                                    if ($choice->file_name != '')
                                    {
                                        $old_cfile_name = "Choice_".$cid."_".$choice->file_name;
                                        $this->deleteRecordingFile($path . $old_cfile_name);
                                    }

                                    $choice->file_name = $_FILES['choice_recording_file']['name'][$cno];
                                }
                                
                                if ($errors) print_r($errors);
                            }
                        }
                    $cno = $cno + 1;
                    }
                $qno = $qno + 1;
                }
            }
            
        }
    }

    
    protected function updateTimes($survey)
    {
        date_default_timezone_set('Asia/Dubai');
        $date = date_create(); 

        if($survey->time_created == '0000-00-00 00:00:00'){
            $survey->time_created = date_format($date, 'Y-m-d H:i:s');
            $survey->time_modified = date_format($date, 'Y-m-d H:i:s');
        }
        else
        {
            $survey->time_modified = date_format($date, 'Y-m-d H:i:s');
        }
    }
    /**
     * Update a survey based on POST parameters
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function editSurvey(&$request)
    {

        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
        //print_r($_REQUEST);
        
        $this->pdo->beginTransaction();

        // Get survey from database or create a new survey object
        $survey = $this->getSurvey($request);

        //print_r($survey);
        // Set values on survey object
        $this->setSurveyValues($survey, $request);

        $this->storeSurveyRemoveDeletedQuestionsChoicesRecordings($survey);
        // Store survey, question and choice records in database
        
        //print_r($survey);
        // Store survey, question and choice recording files in recording folder
        $this->saveRecordings($survey);

        //print_r("Done saving recordings");
        
        $this->updateTimes($survey);
        //print_r($survey);
        // Store survey, question and choice records in database
        $this->storeSurvey($survey);
        

        $this->pdo->commit();
        //print_r($_REQUEST);

        $this->redirect('survey_edit.php?survey_id=' . $survey->survey_id . '&status=success');
        
    }

    /**
     * Update a survey based on POST parameters and then continue to editing properties
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function editandContinue(&$request)
    {

        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
        
        $this->pdo->beginTransaction();

        // Get survey from database or create a new survey object
        $survey = $this->getSurvey($request);

        // Set values on survey object
        $this->setSurveyValues($survey, $request);

        $this->storeSurveyRemoveDeletedQuestionsChoicesRecordings($survey);
        // Store survey, question and choice records in database
        

        // Store survey, question and choice recording files in recording folder
        $this->saveRecordings($survey);

        $this->updateTimes($survey);

        // Store survey, question and choice records in database
        $this->storeSurvey($survey);
        
        $this->pdo->commit();

        $this->redirect('survey_edit_properties.php?survey_id=' . $survey->survey_id);
        
    }
    
    /**
     * Delete a survey based on the survey_id specified in the POST parameters
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function deleteSurvey(&$request)
    {
        if (!empty($request['survey_id']))
        {
            $this->pdo->beginTransaction();

            SurveyCondition::deleteConditionswithConditionSurveyIDs($this->pdo, $request['survey_id']);
            QuestionCondition::deleteConditionswithConditionSurveyIDs($this->pdo, $request['survey_id']);

            $survey = Survey::queryRecordById($this->pdo, $request['survey_id']);
            $survey->deleteRecord($this->pdo);

            $this->pdo->commit();

            $this->redirect('surveys.php?status=deleted');
        }
    }
}

?>
