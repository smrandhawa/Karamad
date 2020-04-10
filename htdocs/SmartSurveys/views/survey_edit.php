<!doctype html>
<html>
<head>
  <title>Survey</title>
  <?php include 'stylesheets.php'; ?>
  <?php include 'scripts.php'; ?>
  <script type="text/javascript">
    var questionHtml = <?php ob_start(); include('survey_edit_question.php'); echo json_encode(ob_get_clean()); ?>;
    var choiceHtml = <?php ob_start(); include('survey_edit_choice.php'); echo json_encode(ob_get_clean()); ?>;
    <?php if (empty($survey->survey_id)): ?>
      $(function()
      {
          $('#survey_name').focus();
      });

    <?php endif; ?>
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
          <form id="survey_edit_form" action="survey_edit.php" method="post" enctype='multipart/form-data'>
            <input type="hidden" id="action" name="action" value="edit_survey" />
            <input type="hidden" id="survey_id" name="survey_id" value="<?php echo htmlspecialchars($survey->survey_id); ?>" />
            <div class="input_form survey_edit">
              <h2>Edit Survey Details</h2>
              <div>
                <label>Survey title:</label>
                <input type="text" id="survey_name" name="survey_name" value="<?php echo htmlspecialchars($survey->survey_name); ?>" />
              </div>

              <div>
                <label style="width: auto;">Brief Description text : </label>
                <input type="text" name="survey_description" value="<?php echo htmlspecialchars($survey->survey_description); ?>" placeholder="Upload Recording of Brief Description using button below and enter corresponding recording text here"/>
              </div>

              <div class="survey_description_recording_container">
                <input type="file" class="inputfilesurveydescription" name="survey_description" id="survey_description" data-multiple-caption="{count} files selected">
                <label <?php if (!empty($survey->file_name_description)): ?> onMouseOver="this.style.backgroundColor='#4b0f31'" onMouseOut="this.style.backgroundColor='#885365'" style="background-color: #885365;width: auto;"<?php endif; ?> <?php if (empty($survey->file_name_description)): ?> style="width: auto;"<?php endif; ?> for="survey_description">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg>

                <?php if(empty($survey->file_name_description)){ 
                ?>
                <span class="textinputfilesurveydescription" > Select Survey Brief Description Recording </span>
                <?php 
                } 
                ?>

                <?php if(!empty($survey->file_name_description)){ 
                ?>
                <span class="textinputfilesurveydescription" > Already Selected Brief Description File : <?= $survey->file_name_description; ?> </span>
                <?php
                }
                ?>

                </label>
              </div>

              <div>
                <label style="width: auto;"> Instructions text : </label>
                <input type="text" name="survey_instructions" value="<?php echo htmlspecialchars($survey->survey_instructions); ?>" placeholder="Upload Recording of Instructions for Workers using button below and enter corresponding recording text here" />
              </div>

              <div class="survey_instruction_recording_container">
                <input type="file" class="inputfilesurveyinstructions" name="survey_instructions" id="survey_instructions" data-multiple-caption="{count} files selected">
                <label <?php if (!empty($survey->file_name_instructions)): ?> onMouseOver="this.style.backgroundColor='#4b0f31'" onMouseOut="this.style.backgroundColor='#885365'" style="background-color: #885365;width: auto;"<?php endif; ?> <?php if (empty($survey->file_name_instructions)): ?> style="width: auto;"<?php endif; ?> for="survey_instructions">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg>

                <?php if(empty($survey->file_name_instructions)){ 
                ?>
                <span class="textinputfilesurveyinstructions" > Select Instructions for Workers Recording </span>
                <?php 
                } 
                ?>

                <?php if(!empty($survey->file_name_instructions)){ 
                ?>
                <span class="textinputfilesurveyinstructions" > Already Selected Instructions File : <?= $survey->file_name_instructions; ?> </span>
                <?php
                }
                ?>

                </label>
              </div>
              <div class="questions_container">
                <h2>Edit Questions</h2>
                <div class="questions">
                  <?php foreach ($survey->questions as $i => $question): ?>
                  <?php include 'survey_edit_question.php'; ?>
                  <?php endforeach; ?>
                </div>
                <div style="margin-top: 40px">
                  <button id="add_question">Add Question</button>
                </div>
              </div>
              <div class="submit_button">
                <button id="delete_survey" name="delete_survey">Delete Survey</button>
                <button id="submitButton" name="submitButton">Save</button>
                <button id="save_continue" name="save_continue">Save & Continue</button>
              </div>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
    <script type="text/javascript" src="js/custom-file-input-file-description.js"></script>
    <script type="text/javascript" src="js/custom-file-input-file-instructions.js"></script>
    <?php include 'footer.php'; ?>
  </div>
</body>
</html>
