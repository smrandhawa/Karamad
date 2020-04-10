<select class="select_question_condition_question select-css" name="select_question_condition_question[<?php if (empty($questioncondition)) echo 'QUESTION_CONDITION_ID'; else echo $questioncondition->getUniqueId(); ?>]">

  <?php if ($survey_selected == 0): ?>
    <option value="-" selected="selected"> -------- </option>
  <?php endif; ?>
  <?php foreach ($survey->independentsurveysquestions[$survey_selected] as $index => $surveyquestion): ?>
    <option value="<?php echo htmlspecialchars($usersurveyid); ?>"<?php if (!empty($questioncondition) && $questioncondition->question_condition_survey_id == $surveyquestion['question_id']): ?> 
      <?php $question_selected = $surveyquestion['question_id']; ?>selected="selected"<?php endif; ?>>
      <?php $qname = $surveyquestion['question_id'];?>
      <?php echo htmlspecialchars('Question No : '.$qname);?>
      <span>&nbsp;&nbsp;</span>
    </option>
  <?php endforeach; ?>
</select>