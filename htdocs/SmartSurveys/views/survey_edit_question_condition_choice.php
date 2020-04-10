<?php if(!empty($survey->independentsurveyidswithradioquestions)): ?>
<div class="question_condition" data-question_condition_id="<?php if (empty($questioncondition)) echo 'QUESTION_CONDITION_ID'; else echo $questioncondition->getUniqueId(); ?>" data-question_condition_number="<?php if (isset($qci)) echo $qci + 1; ?>">
  <label style="width: auto;" >User must have Answered Question&nbsp;:&nbsp;&nbsp;</label>
  <?php $survey_selected = 0; ?>
  <select class="select_question_condition_survey select-css" name="select_question_condition_survey[<?php if (empty($questioncondition)) echo 'QUESTION_CONDITION_ID'; else echo $questioncondition->getUniqueId(); ?>]">
    <?php if (empty($questioncondition)): ?>
      <option value="-" selected="selected"> -------- </option>
    <?php endif; ?>
    <?php foreach ($survey->independentsurveyidswithradioquestions as $index => $usersurveyid): ?>
      <option value="<?php echo htmlspecialchars($usersurveyid); ?>"<?php if (!empty($questioncondition) && $questioncondition->question_condition_survey_id == $usersurveyid): ?> 
        <?php $survey_selected = $usersurveyid; ?>selected="selected"<?php endif; ?>>
        <?php $sname = $survey->independentsurveynameswithradioquestions[$index];?>
        <?php if (strlen($sname) > 40){$sname = mb_substr($sname, 0, 39);$sname = $sname.'...'; }?>
        <?php echo htmlspecialchars('Survey ID '.$usersurveyid.' : '. $sname);?> 
        <!--<?php echo htmlspecialchars($sname);?>--> 
        <span>&nbsp;&nbsp;</span>
      </option>
    <?php endforeach; ?>
  </select>

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

<!--
<?php $usersurveyquestions = $survey->getQuestions($survey_selected); ?>
    <?php foreach ($usersurveyquestions as $index => $usersurveyquestion): ?>
      <option value="<?php echo htmlspecialchars($usersurveyid); ?>"<?php if (!empty($questioncondition) && $questioncondition->question_condition_survey_id == $usersurveyid): ?> selected="selected"<?php endif; ?>>
        <?php $sname = $survey->usersurveynamesindependent[$index];?>
        <?php if (strlen($sname) > 40){$sname = mb_substr($sname, 0, 39);$sname = $sname.'...'; }?>
        <?php echo htmlspecialchars('Survey ID '.$usersurveyid.' : '. $sname);?> 
        <?php echo htmlspecialchars($sname);?>
        <span>&nbsp;&nbsp;</span>
      </option>
    <?php endforeach; ?>
--> 

  </select>

  <span>&nbsp;&nbsp;</span>
  <!--<button class="delete_condition">Delete Condition</button>-->
</div>

<?php elseif(!empty($survey->usersurveyids)) : ?>
<div id="warning_text" >
<label name="empty_surveys_warning" style="width: auto;" > Sorry, This survey is defined as a pre-requisite condition in all your available surveys. Add other independent surveys first to enable conditions on them. &nbsp;&nbsp;&nbsp;</label>
</div>
<?php else : ?>
<div id="warning_text" >
<label name="empty_surveys_warning" style="width: auto;" > Sorry, This is your only survey. Add other surveys first to enable conditions on them. &nbsp;&nbsp;&nbsp;</label>
</div>
<?php endif; ?>