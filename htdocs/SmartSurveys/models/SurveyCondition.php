<?php

/**
 * The SurveyCondition class is a Model representing the survey_condition table, used to store conditions
 * associated with a survey
 *
 */
class SurveyCondition extends Model
{
    // The primary key used to uniquely identify a record
    protected static $primaryKey = 'condition_id';

    // The list of fields in the table
    protected static $fields = array(
        'condition_id',
        'survey_id',
        'complete_condition_survey_id'
    );

    /**
     * Get a unique id for this object, using the primary key if the record
     * has been stored in the database, otherwise a generated unique id
     * @return string|int returns a unique id
     */
    public function getUniqueId()
    {
        if (!empty($this->condition_id))
            return $this->condition_id;
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
     * @param array $condition_ids the array of condition ids to delete
     */
    public static function deleteConditions(PDO $pdo, $condition_ids)
    {
        if (!empty($condition_ids))
        {
            $sql = 'delete from survey_condition where condition_id in (' . implode(',', array_fill(0, count($condition_ids), '?')) . ')';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($condition_ids));
        }
    }

    /**
     * Delete conditions if completion survey is deleted on which these condition were depending upon
     *
     * @param PDO $pdo the database to search in
     * @param array $condition_ids the array of condition ids to delete
     */
    public static function deleteConditionswithConditionSurveyIDs(PDO $pdo, $complete_condition_survey_id)
    {
        if (!empty($complete_condition_survey_id))
        {
            $sql = 'delete from survey_condition where complete_condition_survey_id = '.$complete_condition_survey_id;
            print_r($sql);
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
    }
}

?>
