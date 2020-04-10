<?php

/**
 * The Survey class is a Model representing the survey table, used to get questions 
 * associated with the survey and to get survey responses and survey counts for reporting
 */
class Survey extends Model
{
    // The primary key used to uniquely identify a record
    protected static $primaryKey = 'survey_id';

    // The list of fields in the table
    protected static $fields = array(
        'survey_id',
        'requester_id',
        'survey_name',
        'time_created',
        'time_modified',
        'file_name_description',
        'file_name_instructions',
        'survey_description',
        'survey_instructions',
        'no_of_respondents',
        'min_reward_per_response',
        'max_reward_per_response'
    );

    public $questions = array();
    public $conditions = array();
    public $responses = array();
    public $questionconditions = array();
    public $usersurveyidsindependent = array();
    public $usersurveynamesindependent = array();
    public $independentsurveyidswithradioquestions = array();
    public $independentsurveynameswithradioquestions = array();
    public $independent_radio_surveys_questions_choices = array();
    public $usersurveyids = array();
    public $totalnoofconditions = 0;
    public $totalnoofquestions =0;

     /**
     * Checking recursively if one survey is dependent on other survey directly or indirectly based upon Survey 
     * Completion Condition
     *
     * @param PDO $pdo the database to search in, $sid the base survey id from where condition is added
     *         $csid the conditional survey id for which we are checking if it's dependents on base survey id or not
     */
    public function checkifTransivetlyDependentSurveyCompletionCondition(PDO $pdo,$sid,$csid)
    {
      $condition_survey_ids_sql = 'select complete_condition_survey_id from survey_condition where survey_id = '.$csid;
      $condition_survey_ids = Survey::querySqlandReturnArrayResult($pdo, $condition_survey_ids_sql);

      if(!empty($condition_survey_ids))
        {
           foreach ($condition_survey_ids as $idarrayindex => $idarray)
           {  
              if(!empty($idarray))
              {
                if ($idarray['complete_condition_survey_id'] == $sid)
                {
                  return false;
                }
                else
                {
                  return Survey::checkifTransivetlyDependentSurveyCompletionCondition($pdo,$sid,$idarray['complete_condition_survey_id']);
                }
              }
           }   
        }
      return true;
    }

     /**
     * Checking recursively if one survey is dependent on other survey directly or indirectly based upon Question 
     * Answered Condition
     *
     * @param PDO $pdo the database to search in, $sid the base survey id from where condition is added
     *         $csid the conditional survey id for which we are checking if it's dependents on base survey id or not
     */
    public function checkifTransivetlyDependentQuestionAnsweredCondition(PDO $pdo,$sid,$csid)
    {
      $condition_survey_ids_sql = 'select question_condition_survey_id from question_condition where survey_id = '.$csid;
      $condition_survey_ids = Survey::querySqlandReturnArrayResult($pdo, $condition_survey_ids_sql);

      if(!empty($condition_survey_ids))
        {
           foreach ($condition_survey_ids as $idarrayindex => $idarray)
           {  
              if(!empty($idarray))
              {
                if ($idarray['question_condition_survey_id'] == $sid)
                {
                  return false;
                }
                else
                {
                  return Survey::checkifTransivetlyDependentQuestionAnsweredCondition($pdo,$sid,$idarray['question_condition_survey_id']);
                }
              }
           }   
        }
      return true;
    }

     /**
     * Get all independent surveys (surveys not transivitely or directly dependent on other surveys) of user
     *
     * @param PDO $pdo the database to search in
     */
    public function getUserSurveysTransit(PDO $pdo)
    {
      $user = $_SESSION['login'];

      $sql_ids_names = 'SELECT survey_id,survey_name FROM survey where requester_id = ' . $user->requester_id. ' and survey_id != ' . $this->survey_id;

      $surveyidsnamesofuser = Survey::querySqlandReturnArrayResult($pdo, $sql_ids_names);

      if(!empty($surveyidsnamesofuser))
      {
        foreach ($surveyidsnamesofuser as $idnamearrayindex => $idnamearray)
          {  
            if(!empty($idnamearray))
            {
              if ((Survey::checkifTransivetlyDependentSurveyCompletionCondition($pdo,$this->survey_id,$idnamearray['survey_id'])) && (Survey::checkifTransivetlyDependentQuestionAnsweredCondition($pdo,$this->survey_id,$idnamearray['survey_id'])))
              {
                $this->usersurveyidsindependent[] = $idnamearray['survey_id'];
                $this->usersurveynamesindependent[] = $idnamearray['survey_name'];

                $temp_radio_questions_choices = Survey::getSurveyRadioQuestions($pdo,$idnamearray['survey_id']);
                if(!empty($temp_radio_questions_choices))
                {
                  $this->independentsurveyidswithradioquestions[] = $idnamearray['survey_id'];
                  $this->independentsurveynameswithradioquestions[] = $idnamearray['survey_name'];
                  foreach($temp_radio_questions_choices as $radio_question_chocie)
                  {
                    $this->independent_radio_surveys_questions_choices[] = strval($idnamearray['survey_id']).'_'.strval($idnamearray['survey_name']).'_'.$radio_question_chocie;
                  }
                }
              }

              $this->usersurveyids[] = $idnamearray['survey_id'];
            }
         }   
      }
    }

     /**
     * Get all surveys of user
     *
     * @param PDO $pdo the database to search in
     */
    public function getUserSurveysNew(PDO $pdo)
    {
        $user = $_SESSION['login'];
        $sql_ids_names = 'SELECT survey_id,survey_name FROM survey where requester_id = ' . $user->requester_id;

        $surveyidsnamesofuser = Survey::querySqlandReturnArrayResult($pdo, $sql_ids_names);

        if(!empty($surveyidsnamesofuser))
        {
             foreach ($surveyidsnamesofuser as $idnamearrayindex => $idnamearray)
             {  
                if(!empty($idnamearray))
                {
                    $this->usersurveyidsindependent[] = $idnamearray['survey_id'];
                    $this->usersurveynamesindependent[] = $idnamearray['survey_name'];
                }
             }   
        }

        $this->usersurveyids = $this->usersurveyidsindependent;
    }
     /**
     * Get the name of survey when given its unique id
     *
     * @param PDO $pdo the database to search in
     */
    public function getSurveyNamefromid(PDO $pdo,$sid)
    {
        
        $sql_name = 'select survey_name from survey where survey_id = ' . $sid;
        $surveynameofuser = Survey::querySqlandReturnArrayResult($pdo, $sql_name);

        if(!empty($surveynameofuser))
        {
            return ($surveynameofuser[0]['survey_name']);
        }
    }
    /**
     * Get the radio question choices records associated with this survey, and assign to
     * $question_choice_pair instance variable
     *
     * @param PDO $pdo the database to search in
     */
    public function getRadioQuestionsChoicePairforDependency(PDO $pdo)
    {
        return Survey::getSurveyRadioQuestions($pdo,$this->survey_id );
    }

    /**
     * Get the question records associated with this survey, and assign to
     * $questions instance variable
     *
     * @param PDO $pdo the database to search in
     */
    public function getQuestions(PDO $pdo)
    {
        $search = array('survey_id' => $this->survey_id, 'sort' => 'question_order');
        $this->questions = Question::queryRecords($pdo, $search);
        $this->totalnoofquestions = sizeof($this->questions);
    }

    /**
     * Get the radio questions id,order_no data associated with the a specific survey $sid, and assign to
     * $radioquestions instance variable
     *
     * @param PDO $pdo the database to search in, Survey id $sid for which radio question we need
     */
    public function getSurveyRadioQuestions(PDO $pdo, $sid)
    {

        $sql_questions = "SELECT question_id,question_order FROM `question` where survey_id = $sid and question_type = 'radio'";
        //print_r($sql_questions);
        $tmpradioquestions = Question::querySqlandReturnArrayResult($pdo, $sql_questions);
        $radio_questions_choices = array();
        if(!empty($tmpradioquestions))
        {
          foreach($tmpradioquestions as $radioquestion)
          {
            $radioquestionchoices = Survey::getRadioQuestionChoices($pdo,$radioquestion['question_id']);
            foreach($radioquestionchoices as $radiochoice)
            {
              $radio_questions_choices[] = strval($radioquestion['question_id']).'_'.strval($radioquestion['question_order']).'_'.strval($radiochoice['choice_id']).'_'.strval($radiochoice['choice_order']);
            }
          }
        }

        return $radio_questions_choices;
    }

    /**
     * Get the choices id,order_no data associated with the a specific radio question $qid, and assign to
     * $radioquestionchoices instance variable
     *
     * @param PDO $pdo the database to search in, Question id $qid for which choices we need
     */
    public function getRadioQuestionChoices(PDO $pdo, $qid)
    {

        $sql_choices = "SELECT choice_id,choice_order FROM `choice` where question_id = $qid";
        $radioquestionchoices = Choice::querySqlandReturnArrayResult($pdo, $sql_choices);

        if(!empty($radioquestionchoices))
        {
            return $radioquestionchoices;
        }

        return array();
    }

    /**
     * Get the total number of survey completion plus question answered conditions records associated 
     * with this survey, and assign to $totalnoofconditions instance variable
     * 
     * @param PDO $pdo the database to search in
     */
    public function getTotalNumberofConditions(PDO $pdo)
    {
        $search = array('survey_id' => $this->survey_id, 'sort' => 'condition_id');
        $total_survey_completion_conditions = sizeof(SurveyCondition::queryRecords($pdo, $search));

        $search = array('survey_id' => $this->survey_id, 'sort' => 'question_condition_id');
        $total_question_answered_conditions = sizeof(QuestionCondition::queryRecords($pdo, $search));

        $this->totalnoofconditions = $total_survey_completion_conditions + $total_question_answered_conditions;
    }

    /**
     * Get the condition records associated with this survey, and assign to
     * $conditions instance variable
     *
     * @param PDO $pdo the database to search in
     */
    public function getConditions(PDO $pdo)
    {
        $search = array('survey_id' => $this->survey_id, 'sort' => 'condition_id');
        $this->conditions = SurveyCondition::queryRecords($pdo, $search);
    }

    /**
     * Get the question condition records associated with this survey, and assign to
     * $questionconditions instance variable
     *
     * @param PDO $pdo the database to search in
     */
    public function getQuestionConditions(PDO $pdo)
    {
        $search = array('survey_id' => $this->survey_id, 'sort' => 'question_condition_id');
        $this->questionconditions = QuestionCondition::queryRecords($pdo, $search);
    }

    /**
     * Get all survey responses for this survey and assign it to the
     * $responses instance variable
     *
     * @param PDO $pdo the database to search in
     */
    public function getSurveyResponses(PDO $pdo)
    {
        if (!empty($this->survey_id))
        {
            if (empty($this->questions))
                $this->getQuestions($pdo);

            $questionSubSelects = array();
            foreach ($this->questions as $question)
            {
                $questionSubSelects[] = "(select group_concat(answer_value, ', ') from survey_answer sa
                                          where sa.survey_response_id = sr.survey_response_id and
                                          sa.question_id = :question_id_{$question->question_id}) as question_{$question->question_id}";
                $params["question_id_{$question->question_id}"] = $question->question_id;
            }
            $questionSubSelectSql = implode(', ', $questionSubSelects);
            $sql = "select sr.*, $questionSubSelectSql from survey_response sr where sr.survey_id = :survey_id";
            $params['survey_id'] = $this->survey_id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            $this->responses = $stmt->fetchAll();
        }
    }

    /**
     * Get the survey response counts for every non-open text question for this survey 
     * and assign it to the $choice_counts instance variable of each question object
     *
     * @param PDO $pdo the database to search in
     */
    public function getSurveyResponseCounts(PDO $pdo)
    {
        foreach ($this->questions as $i => $question)
        {
            if (! in_array($question->question_type, array('radio', 'checkbox')))
                unset($this->questions[$i]);
        }

        foreach ($this->questions as $i => $question)
        {
            $sql = "select count(*) from survey_answer sa
                    left outer join survey_response sr on sr.survey_response_id = sa.survey_response_id
                    where sr.survey_id = :survey_id
                    and sa.question_id = :question_id
                    and sa.answer_value = :answer_value";
            $stmt = $pdo->prepare($sql);

            $question->max_answer_count = 0;

            foreach ($question->choices as $choice)
            {
                $params = array(
                    'survey_id' => $this->survey_id,
                    'question_id' => $question->question_id,
                    'answer_value' => $choice->choice_text
                );
                $stmt->execute($params);
                $stmt->setFetchMode(PDO::FETCH_NUM);
                if ($row = $stmt->fetch())
                {
                    $choice->answer_count = $row[0];
                    if ($choice->answer_count > $question->max_answer_count)
                        $question->max_answer_count = $choice->answer_count;
                }
            }

            $question->choice_counts = array();
            foreach ($question->choices as $choice)
                $question->choice_counts[] = array($choice->choice_text, $choice->answer_count);
        }
    }

    /**
     * Get a unique id for this object, using the primary key if the record
     * has been stored in the database, otherwise a generated unique id
     * @return string|int returns a unique id
     */
    public function getUniqueId()
    {
        if (!empty($this->survey_id))
            return $this->survey_id;
        else
        {
            static $uniqueID;
            if (empty($uniqueID))
                $uniqueID = __CLASS__ . uniqid();
            return $uniqueID;
        }
    }
}

?>
