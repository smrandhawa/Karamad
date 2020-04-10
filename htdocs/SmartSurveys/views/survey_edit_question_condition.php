<?php if(!empty($survey->independent_radio_surveys_questions_choices)): ?>
<div class="question_condition" data-question_condition_id="<?php if (empty($questioncondition)) echo 'QUESTION_CONDITION_ID'; else echo $questioncondition->getUniqueId(); ?>" data-question_condition_number="<?php if (isset($qci)) echo $qci + 1; ?>">
  <label style="width: 30%;vertical-align: top; text-align-last: end;" >User must have Answered Question and chose a specific choice&nbsp;:&nbsp;&nbsp;</label>
  <select class="select_question_condition select-css" name="select_question_condition[<?php if (empty($questioncondition)) echo 'QUESTION_CONDITION_ID'; else echo $questioncondition->getUniqueId(); ?>]">
    <?php if (empty($questioncondition)): ?>
      <option value="-" selected="selected"> -- Select -- </option>
    <?php endif; ?>
    <?php $cnoditionsurvey = array(); ?>independentsurveyidswithradioquestions
    <?php foreach ($survey->independentsurveynameswithradioquestions as $survey_name_index => $surveynamewithradioquestions): ?>
      <?php $sname = $surveynamewithradioquestions;?>
      <?php if (strlen($sname) > 40){$sname = mb_substr($sname, 0, 39);$sname = $sname.'...'; }?>
      <optgroup label="<?php echo htmlspecialchars($sname); ?>">
        <?php foreach ($survey->independent_radio_surveys_questions_choices as $index => $survey_question_choice): ?>
        <?php $survey_question_choice_tokens = explode('_',$survey_question_choice,6); ?>
          <?php if ($survey->independentsurveyidswithradioquestions[$survey_name_index] == $survey_question_choice_tokens[0]): ?>
          <option value="<?php echo htmlspecialchars($survey_question_choice); ?>"<?php if (!empty($questioncondition) && ($questioncondition->question_condition_survey_id == intval($survey_question_choice_tokens[0])) && ($questioncondition->question_id == intval($survey_question_choice_tokens[2])) && ($questioncondition->choice_id == intval($survey_question_choice_tokens[4])) ): ?> selected="selected"<?php endif; ?>>
            <?php echo htmlspecialchars('Survey : ' . $sname);?>
            <span>&ndash;&ndash;</span>
            <?php echo htmlspecialchars('Question ' . $survey_question_choice_tokens[3]);?>
            <span>&ndash;&ndash;</span>
            <?php echo htmlspecialchars('Choice '. $survey_question_choice_tokens[5]);?>
            <span>&nbsp;&nbsp;</span>
          </option>
        <?php endif; ?>  
        <?php endforeach; ?>
    </optgroup>
    <?php endforeach; ?>
  </select>
  <button class="delete_question_condition">Delete Condition</button>
</div>
<?php endif; ?>