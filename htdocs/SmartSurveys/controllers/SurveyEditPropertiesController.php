<?php

/**
 * The SurveyEditController class is a Controller that allows a user to enter a survey
 * or make changes to an existing survey
 *
 */
class SurveyEditPropertiesController extends Controller
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
            $this->assign('statusMessage', 'Survey properties updated successfully @'. date_format($date, ' Y-m-d H:i:s') );
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
                break;

            case 'edit_survey':
                $this->editSurvey($request);
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

            $survey->getUserSurveysTransit($this->pdo);
            // Keep track of existing ids so that any records not updated are deleted
            $survey->existing_question_condition_ids = array();
            $survey->existing_condition_ids = array();        
            
            $survey->getTotalNumberofConditions($this->pdo);

            $survey->getConditions($this->pdo);
            foreach ($survey->conditions as $condition) {
                $survey->existing_condition_ids[] = $condition->condition_id;
            }

            $survey->getQuestionConditions($this->pdo);
            foreach ($survey->questionconditions as $question_condition) {
                $survey->existing_question_condition_ids[] = $question_condition->question_condition_id;
            }       
            
            $survey = $this->getSurveyRecordingProperties($survey);
            $survey = $this->getSurveyServicePrompts($survey);
            $survey->total_airtime_cost_without_tax = floatval(((($survey->recording_content_size + $survey->service_prompts_content_size)/15) + 1) * 0.7925);
            $survey->tax = floatval(($survey->total_airtime_cost_without_tax * 100) / 88);
            $survey->total_airtime_cost_with_tax = ceil($survey->total_airtime_cost_without_tax + $survey->tax);
            $survey->minimum_profit_worker = $survey->min_reward_per_response - $survey->total_airtime_cost_with_tax;
        
            $survey->maximum_profit_worker = $survey->max_reward_per_response - $survey->total_airtime_cost_with_tax;

        }

        return $survey;
    }


    function wavDur($file)
    {
      $fp = fopen($file, 'r');
      if (fread($fp,4) == 'RIFF') 
        {
          fseek($fp, 20);
          $rawheader = fread($fp, 16);
          $header = unpack('vtype/vchannels/Vsamplerate/Vbytespersec/valignment/vbits',$rawheader);
          $pos = ftell($fp);
          while (fread($fp,4) != "data" && !feof($fp)) {
              $pos++;
              fseek($fp,$pos);
          }
          $rawheader = fread($fp, 4);
          $data = unpack('Vdatasize',$rawheader);
          $sec = $data['datasize']/$header['bytespersec'];
          return $sec;
        }
    }

    /**
     * get the values for the survey service prompts content
     *
     * @param Survey $survey the survey object to calculate service prompts content size
     * @return Survey $survey returns a Survey object with service prompts information fields
     */
    protected function getSurveyServicePrompts(Survey $survey)
    {
        
        $survey->service_prompts = array();
        $survey->service_prompts[] = "sil250.wav";
        $survey->service_prompts[] = "Intro.wav";
        $survey->service_prompts[] = "sil250.wav";
        $survey->service_prompts[] = "BriefDescription.wav";
        $survey->service_prompts[] = "MainMenuPrompt0.wav";
        $survey->service_prompts[] = "new_task_alert.wav";
        $survey->service_prompts[] = "ye_kam.wav";
        $survey->service_prompts[] = "ka_hai.wav";
        $survey->service_prompts[] = "50_rupee.wav";
        $survey->service_prompts[] = "surveymenuprompt.wav";
        $survey->service_prompts[] = "task_start_alert.wav";
        $survey->service_prompts[] = "task_details.wav";

        $survey->getQuestions($this->pdo);

        $qno = 1;
        foreach ($survey->questions as $question)
        {
            $survey->service_prompts[] = strval($qno)."_sawaal.wav ";
            $survey->service_prompts[] = "sil250.wav";
            $survey->service_prompts[] = "sil250.wav";
            if ($question->question_type == "radio")
            {
                $survey->service_prompts[] = "info_multi_1.wav ";
                $question->getChoices($this->pdo);
                $cno = 1;
                foreach ($question->choices as $choice)
                {
                    $survey->service_prompts[] = strval($cno)."_option.wav";
                    $survey->service_prompts[] = "sil250.wav";
                    $survey->service_prompts[] = "sil250.wav";
                    $survey->service_prompts[] = "info_multi_2.wav";
                    $survey->service_prompts[] = "sil250.wav";
                    $survey->service_prompts[] = strval($cno)."_dubaiye.wav";
                    $survey->service_prompts[] = "sil250.wav";
                    $cno++;
                }      
            }
            else
            {
                $survey->service_prompts[] = "info_open_end_sawaal_1.wav";
                $survey->service_prompts[] = "sil250.wav";
                $survey->service_prompts[] = "info_open_end_sawaal_2.wav";
                $survey->service_prompts[] = "sil250.wav";
                $survey->service_prompts[] = "aap_nay_ye_record_karwaya_hai.wav";
                $survey->service_prompts[] = "sil250.wav";
                $survey->service_prompts[] = "agar_ye_theek_hai.wav";
                $survey->service_prompts[] = "sil250.wav";
            }

            $qno++;
        }

        //print_r($survey->media_files);
        $survey->no_of_service_prompts = sizeof($survey->service_prompts);
        $survey->service_prompts_content_size = 0;
        $survey->service_prompts_minutes = 0;
        $survey->service_prompts_seconds = 0;
        if(sizeof($survey->service_prompts))
        {
            $dir = "D:/xampp/htdocs/SmartSurveys/SSprompts/";

            // Open a directory, and read its contents
            if (is_dir($dir))
            {
                foreach ($survey->service_prompts as $pfile) 
                {
                    if(is_file($dir.$pfile))
                    {
                        $survey->service_prompts_content_size += $this->wavDur($dir.$pfile);
                    }
                }
            }

            $survey->service_prompts_minutes = intval(($survey->service_prompts_content_size / 60) % 60);
            $survey->service_prompts_seconds = intval($survey->service_prompts_content_size % 60);
        }
        
        return $survey;
 
    }

    /**
     * get the values for the survey recordings content
     *
     * @param Survey $survey the survey object to calculate media content size
     * @return Survey $survey returns a Survey object with media information fields
     */
    protected function getSurveyRecordingProperties(Survey $survey)
    {
        
        $survey->media_files = array();
        $survey->media_files[] = "Survey_Description_".$survey->survey_id."_".$survey->file_name_description;
        $survey->media_files[] = "Survey_Instructions_".$survey->survey_id."_".$survey->file_name_instructions;
        $survey->getQuestions($this->pdo);
        foreach ($survey->questions as $question)
        {
            $survey->media_files[] = "Question_".$question->question_id."_".$question->file_name;
            $question->getChoices($this->pdo);

            foreach ($question->choices as $choice)
                $survey->media_files[] = "Choice_".$choice->choice_id."_".$choice->file_name;
        }

        //print_r($survey->media_files);
        $survey->no_of_media_files = sizeof($survey->media_files);
        $survey->recording_content_size = 0;
        $survey->recording_minutes = 0;
        $survey->recording_seconds = 0;
        if(sizeof($survey->media_files))
        {
            $dir = "D:/xampp/htdocs/SmartSurveys/recordings/";

            // Open a directory, and read its contents
            if (is_dir($dir))
            {
                foreach ($survey->media_files as $rfile) 
                {
                    if(is_file($dir.$rfile))
                    {
                        $survey->recording_content_size += $this->wavDur($dir.$rfile);
                        //return str_pad($minutes,2,"0", STR_PAD_LEFT).":".str_pad($seconds,2,"0", STR_PAD_LEFT);
                    }
                }
            }

            $survey->recording_minutes = intval(($survey->recording_content_size / 60) % 60);
            $survey->recording_seconds = intval($survey->recording_content_size % 60);
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
        
        $survey->updateValues($request);

        $this->setSurveyConditions($survey, $request);

        $this->setSurveyQuestionConditions($survey, $request);

        $survey->minimum_profit_worker = $survey->min_reward_per_response - $survey->total_airtime_cost_with_tax;
        
        $survey->maximum_profit_worker = $survey->max_reward_per_response - $survey->total_airtime_cost_with_tax;
    }

    /**
     * Set the survey's question conditions based on form parameters
     *
     * @param Survey $survey the survey object to update
     * @param array $request the page parameters from a form post or query string
     */
    protected function setSurveyQuestionConditions(Survey $survey, &$request)
    {
        $survey->questionconditions = array();
        if ((isset($request['select_question_condition'])) && (!empty($request['select_question_condition'])))
        {
            foreach ($request['select_question_condition'] as $questionconditionID => $questionconditionstring)
            {
                $questionconditionstringtoken = explode('_',$questionconditionstring,6);
                $questioncondition = new QuestionCondition;
                if (is_numeric($questionconditionID))
                    $questioncondition->question_condition_id = $questionconditionID;

                $questioncondition->question_condition_survey_id = intval($questionconditionstringtoken[0]);
                $questioncondition->question_id = intval($questionconditionstringtoken[2]);
                $questioncondition->choice_id = intval($questionconditionstringtoken[4]);
                $survey->questionconditions[] = $questioncondition;
            }

        }
    }

    /**
     * Set the survey's conditions based on form parameters
     *
     * @param Survey $survey the survey object to update
     * @param array $request the page parameters from a form post or query string
     */
    protected function setSurveyConditions(Survey $survey, &$request)
    {
        $survey->conditions = array();
        if ((isset($request['select_condition'])) && (!empty($request['select_condition'])))
        {
            foreach ($request['select_condition'] as $conditionID => $completeSurveyID)
            {
                $condition = new SurveyCondition;
                if (is_numeric($conditionID))
                    $condition->condition_id = $conditionID;
                $condition->complete_condition_survey_id = $completeSurveyID;          
                $survey->conditions[] = $condition;
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
        $stored_condition_ids = array();
        $stored_question_condition_ids = array();

        $survey->storeRecord($this->pdo);

        foreach ($survey->conditions as $condition) 
        {
            $condition->survey_id = $survey->survey_id;
            $condition->storeRecord($this->pdo);
            $stored_condition_ids[] = $condition->condition_id;
        }

        foreach ($survey->questionconditions as $questioncondition) 
        {
            $questioncondition->survey_id = $survey->survey_id;
            $questioncondition->storeRecord($this->pdo);
            $stored_question_condition_ids[] = $questioncondition->question_condition_id;
        }

        // Delete Question Conditions that were removed
        if (!empty($survey->existing_question_condition_ids))
        {
            $deleted_question_condition_ids = array_diff($survey->existing_question_condition_ids, $stored_question_condition_ids);
            QuestionCondition::deleteConditions($this->pdo, $deleted_question_condition_ids);
        }

        // Delete Conditions that were removed
        if (!empty($survey->existing_condition_ids))
        {
            $deleted_condition_ids = array_diff($survey->existing_condition_ids, $stored_condition_ids);
            SurveyCondition::deleteConditions($this->pdo, $deleted_condition_ids);
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
        
        $this->pdo->beginTransaction();

        // Get survey from database
        $survey = $this->getSurvey($request);

        // Set values on survey object
        $this->setSurveyValues($survey, $request);

        $this->updateTimes($survey);

        // Store survey properties records in database
        $this->storeSurvey($survey);
    
        $this->pdo->commit();

        $this->redirect('survey_edit_properties.php?survey_id=' . $survey->survey_id . '&status=success');
        
    }

}

?>
