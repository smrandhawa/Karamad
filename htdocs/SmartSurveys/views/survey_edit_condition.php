<?php if(!empty($survey->usersurveyidsindependent)): ?>
<div class="condition" data-condition_id="<?php if (empty($surveycondition)) echo 'CONDITION_ID'; else echo $surveycondition->getUniqueId(); ?>" data-condition_number="<?php if (isset($ci)) echo $ci + 1; ?>">
  <label style="width: auto;" >User must have completed Survey Task&nbsp;:&nbsp;&nbsp;</label>
  <select class="select_condition select-css" name="select_condition[<?php if (empty($surveycondition)) echo 'CONDITION_ID'; else echo $surveycondition->getUniqueId(); ?>]">
    <?php if (empty($surveycondition)): ?>
      <option value="-" selected="selected"> -------- </option>
    <?php endif; ?>
    <?php foreach ($survey->usersurveyidsindependent as $index => $usersurveyid): ?>
      <option value="<?php echo htmlspecialchars($usersurveyid); ?>"<?php if (!empty($surveycondition) && $surveycondition->complete_condition_survey_id == $usersurveyid): ?> selected="selected"<?php endif; ?>>
        <?php $sname = $survey->usersurveynamesindependent[$index];?>
        <?php if (strlen($sname) > 40){$sname = mb_substr($sname, 0, 39);$sname = $sname.'...'; }?>  
        <?php echo htmlspecialchars('Survey ID '.$usersurveyid.' : '. $sname);?>
        <span>&nbsp;&nbsp;</span>
      </option>
    <?php endforeach; ?>
  </select>
  <button class="delete_condition">Delete Condition</button>
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