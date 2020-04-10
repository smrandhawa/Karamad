<?php

/**
 * The QuestionCondition class is a Model representing the question_condition table, used to store questions answered 
 * conditions associated with a survey
 *
 */
class QuestionCondition extends Model
{
    // The primary key used to uniquely identify a record
    protected static $primaryKey = 'question_condition_id';

    // The list of fields in the table
    protected static $fields = array(
        'question_condition_id',
        'survey_id',
        'question_condition_survey_id',
        'question_id',
        'choice_id'
    );

    /**
     * Get a unique id for this object, using the primary key if the record
     * has been stored in the database, otherwise a generated unique id
     * @return string|int returns a unique id
     */
    public function getUniqueId()
    {
        if (!empty($this->question_condition_id))
            return $this->question_condition_id;
        else
        {
            static $uniqueID;
            if (empty($uniqueID))
                $uniqueID = __CLASS__ . mt_rand(1000, 999999);
            return $uniqueID;
        }
    }

    /**
     * Delete an array of condition ids
     *
     * @param PDO $pdo the database to search in
     * @param array $question_condition_ids the array of question condition ids to delete
     */
    public static function deleteConditions(PDO $pdo, $question_condition_ids)
    {
        if (!empty($question_condition_ids))
        {
            $sql = 'delete from question_condition where question_condition_id in (' . implode(',', array_fill(0, count($question_condition_ids), '?')) . ')';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($question_condition_ids));
        }
    }

    /**
     * Delete conditions if survey is deleted on which question choices those condition depends
     *
     * @param PDO $pdo the database to search in
     * @param array $question_condition_survey_id 
     */
    public static function deleteConditionswithConditionSurveyIDs(PDO $pdo, $question_condition_survey_id)
    {
        if (!empty($question_condition_survey_id))
        {
            $sql = 'delete from question_condition where question_condition_survey_id = '.$question_condition_survey_id;
            print_r($sql);
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
    }
}

?>
