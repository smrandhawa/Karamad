<!doctype html>
<html>
<head>
  <title>Survey</title>
  <?php include 'stylesheets.php'; ?>
  <?php include 'scripts.php'; ?>
  <script type="text/javascript">
    var conditionHtml = <?php ob_start(); include('survey_edit_condition.php'); echo json_encode(ob_get_clean()); ?>;
    var questionconditionHtml = <?php ob_start(); include('survey_edit_question_condition.php'); echo json_encode(ob_get_clean()); ?>;
  </script>
  <script type="text/javascript" src="js/survey_edit.js"></script>
</head>
<body>
  <div id="main">
    <?php include 'header.php'; ?>
    <div id="site_content">
      <div id="content">

        <?php if (isset($statusMessage)): ?>
          <p class="error"><?php echo htmlspecialchars($statusMessage); ?></p>
        <?php endif; ?>
        <?php if (!empty($survey) && $survey instanceof Survey): ?>
          <form id="survey_edit_form" action="survey_edit_properties.php" method="post" enctype='multipart/form-data'>
            <input type="hidden" id="action" name="action" value="edit_survey" />
            <input type="hidden" id="survey_id" name="survey_id" value="<?php echo htmlspecialchars($survey->survey_id); ?>" />
            <div class="input_form survey_edit">
              <h2>Edit Survey Properties</h2>
              <div>
                <label style="display: inline-block;width: auto; margin-right: 10px;">Survey title : </label>
                <span><?php echo htmlspecialchars($survey->survey_name); ?></span>
              </div>

              <div >
                <label style="display: inline-block;width: auto; margin-right: 10px;" >Total Number of Questions :</label>
                <span><?php echo htmlspecialchars($survey->totalnoofquestions); ?></span>
              </div>

              <div >
                <label style="display: inline-block;width: auto; margin-right: 10px;" >Task Recording's Content Size :</label>
                <span><?php echo htmlspecialchars($survey->recording_minutes); ?> minutes and <?php echo htmlspecialchars($survey->recording_seconds); ?> seconds</span>
              </div>

              <div >
                <label style="display: inline-block;width: auto; margin-right: 10px;" >Service Prompts Size :</label>
                <span><?php echo htmlspecialchars($survey->service_prompts_minutes); ?> minutes and <?php echo htmlspecialchars($survey->service_prompts_seconds); ?> seconds</span>
              </div>

              

              <div>
                <div style="display: inline-block;width: auto; margin-right: 10px;">
                  <span> Minimum Reward per response : </span>
                </div>
                <div style="display: inline-block;">
                  <span class="reward_price_symbol">₨</span>
                  <input class="reward_price_value" min="40" step="1" size="1000" type="number" name="min_reward_per_response" value="<?php echo htmlspecialchars($survey->min_reward_per_response); ?>">
                </div>
                <div style="display: inline-block;width: auto; margin-right: 10px;margin-top: 0px;">
                  <span>*Minimum Price for Task should at least ₨.40 in any case, because of Top-up system restriction. </span>
                </div>
              </div>

              <div>
                <label style="display: inline-block;width: auto;" >Minimum Profit Worker will earn = ₨.</label>
                <span><?php echo htmlspecialchars($survey->minimum_profit_worker); ?> </span>
              </div>

              <div>
                <div style="display: inline-block;width: auto; margin-right: 10px;">
                  <span> Maximum Reward per response : </span>
                </div>
                <div style="display: inline-block;">
                  <span class="reward_price_symbol">₨</span>
                  <input class="reward_price_value" min="40" step="1" size="1000" type="number" name="max_reward_per_response" value="<?php echo htmlspecialchars($survey->max_reward_per_response); ?>">
                </div>
                <div style="display: inline-block;width: auto; margin-right: 10px;margin-top: 0px;">
                  <span>* Set Maximum Price for Task same as Minimum price, if your survey is not dynamic (Dynamic means number of questions change per user responses).</span>
                </div>
              </div>

              <div>
                <label style="display: inline-block;width: auto;" >Maximum Profit Worker will earn = ₨.</label>
                <span><?php echo htmlspecialchars($survey->maximum_profit_worker); ?> </span>
              </div>

              <?php if(!empty($question)): ?><?php endif; ?>
              <div>
                <div style="display: inline-block;width: auto; margin-right: 10px;">
                  <span> Number of respondents : </span>
                </div>
                <div style="display: inline-block;">
                  <input class="reward_price_value" min="1" step="1" size="100" type="number" name="no_of_respondents" value="<?php echo htmlspecialchars($survey->no_of_respondents); ?>">
                </div>
              </div>

              <div class="conditions_container" >
                <h2 class="h2_with_description_note">Edit Survey Conditions </h2>
                <strong class="description_note">(Note : Specify conditions that Workers must meet to attempt this Survey Task)</strong>
                <label style="width: auto;">Total No. of Conditions&nbsp;:&nbsp;&nbsp;</label>
                <label> <?php echo htmlspecialchars($survey->totalnoofconditions); ?> </label>
                <div class="conditions">
                  <?php foreach ($survey->conditions as $ci => $surveycondition): ?>
                  <?php include 'survey_edit_condition.php'; ?>
                  <?php endforeach; ?>
                </div>
                <div class="questionconditions">
                  <?php foreach ($survey->questionconditions as $qci => $questioncondition): ?>
                  <?php include 'survey_edit_question_condition.php'; ?>
                  <?php endforeach; ?>
                </div>
                <div>
                  <div style="margin-top: 15px;display: inline-block;width: fit-content;">
                    <button id="add_condition">Add Survey Completion Condition</button>
                  </div>
                  <div style="margin-top: 15px;display: inline-block;width: fit-content;">
                    <button id="add_question_condition">Add Question Answered Condition</button>
                  </div>
                </div>
              <div class="submit_button">
                <button id="submitButton" name="submitButton">Save Properties</button>
              </div>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
    <?php include 'footer.php'; ?>
  </div>
</body>
</html>
