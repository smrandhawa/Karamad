<div class="question" data-question_id="<?php if (empty($question)) echo 'QUESTION_ID'; else echo $question->getUniqueId(); ?>" data-question_number="<?php if (isset($i)) echo $i + 1; ?>">
  

  <div >
    <button  class="move_question_up"><i class="fa fa-arrow-up fa-1x"></i></button>
    <div style="margin-top:5px;margin-bottom: 5px">
      <h3 style="color:#eb6f65;display: inline">Question <span class="question_number"><?php if (isset($i)) echo $i + 1; ?></span></h3>
      <button style="float: right" class="delete_question">Delete Question</button>
    </div>
    <button class="move_question_down"><i class="fa fa-arrow-down fa-1x"></i></button>
  </div>
  
  <div>
    <label>Question type:</label>
    <select class="question_type" name="question_type[<?php if (empty($question)) echo 'QUESTION_ID'; else echo $question->getUniqueId(); ?>]">
      <option value="input"<?php if (!empty($question) && $question->question_type == 'input'): ?> selected="selected"<?php endif; ?>>Open Ended</option>
      <option value="radio"<?php if (!empty($question) && $question->question_type == 'radio'): ?> selected="selected"<?php endif; ?>>Multiple Choice</option>
      </select>

  </div>

  <?php if(!empty($survey->question_choice_pair)): ?>
  <div class="dependent_container">
    <div class="dependent_checkbox">
      <input type="checkbox" id="dependent<?php if (empty($question)) {echo 'QUESTION_ID';} else {echo $question->getUniqueId();} ?>" name="dependent[<?php if (empty($question)) {echo 'QUESTION_ID';} else {echo $question->getUniqueId();} ?>]" value="1"<?php if (! empty($question) && $question->dependent == 1): ?> checked="checked"<?php endif; ?> />
      <label for="dependent<?php if (empty($question)) {echo 'QUESTION_ID';} else {echo $question->getUniqueId();} ?>" style="width: auto;">Dependent on Previous question</label>
    </div>

    <div class="dependent_dropdown">
    <label style="width: auto;">Select Dependent Question Choice :</label>
    <select class="dependent_question_choice_pair" name="dependent_question_choice_pair[<?php if (empty($question)) echo 'QUESTION_ID'; else echo $question->getUniqueId(); ?>]">
      <?php if(empty($question)): ?>
        <option value="-" selected="selected"> -- Select -- </option>
        <?php foreach ($survey->question_choice_pair as $qcindex => $pair): ?>
          <?php $question_choice_pair_tokens = explode('_',$pair,4); ?>
          <option value="<?php echo htmlspecialchars($pair); ?>">
            <?php echo htmlspecialchars('Question ' . $question_choice_pair_tokens[1]);?>
            <span>&ndash;&ndash;</span>
            <?php echo htmlspecialchars('Choice '. $question_choice_pair_tokens[3]);?>
            <span>&nbsp;&nbsp;</span>
          </option> 
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if(!empty($question)): ?>
        <?php foreach ($survey->question_choice_pair as $qcindex => $pair): ?>
          <?php $question_choice_pair_tokens = explode('_',$pair,4); ?>
          <?php if (intval($question_choice_pair_tokens[1]) < $question->question_order): ?>
            <option value="<?php echo htmlspecialchars($pair); ?>"<?php if (($question->dependent_question_id == intval($question_choice_pair_tokens[0])) && ($question->dependent_choice_id == intval($question_choice_pair_tokens[2])) ): ?> selected="selected"<?php endif; ?>>
              <?php echo htmlspecialchars('Question ' . $question_choice_pair_tokens[1]);?>
              <span>&ndash;&ndash;</span>
              <?php echo htmlspecialchars('Choice '. $question_choice_pair_tokens[3]);?>
              <span>&nbsp;&nbsp;</span>
            </option> 
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
      </select>
    </div>
  </div>
  <?php endif; ?>
  
  <div class="generic_question_multiple_choice_derived_container">
    <div class="generic_question_multiple_choice_derived_checkbox">
      <input type="checkbox" id="generic<?php if (empty($question)) {echo 'QUESTION_ID';} else {echo $question->getUniqueId();} ?>" name="generic[<?php if (empty($question)) {echo 'QUESTION_ID';} else {echo $question->getUniqueId();} ?>]" value="1"<?php if (!empty($question) && $question->generic == 1): ?> checked="checked"<?php endif; ?> />
      <label for="dependent<?php if (empty($question)) {echo 'QUESTION_ID';} else {echo $question->getUniqueId();} ?>" style="width: auto;">Generic Question derived from Multiple Choices</label>
    </div>
  </div>

  <div>
    <label>Question text:</label>
    <input type="text" class="question_text" name="question_text[<?php if (empty($question)) echo 'QUESTION_ID'; else echo $question->getUniqueId(); ?>]" value="<?php if ((!empty($question)) && (!empty($question->question_text))) echo htmlspecialchars($question->question_text); ?>" placeholder="Upload Recording of Question and enter it's corresponding text here"/>
  </div>

  <div class="recording_container">
    <input type="file" class="inputfile" name="question_recording_file[]" id="question_recording_file_<?php if (empty($question)) echo 'QUESTION_ID'; else echo $question->getUniqueId(); ?>" data-multiple-caption="{count} files selected">
    
    <label <?php if (!empty($question->file_name)): ?> onMouseOver="this.style.backgroundColor='#4b0f31'" onMouseOut="this.style.backgroundColor='#885365'" style="background-color: #885365;width: auto;"<?php endif; ?> <?php if (empty($question) || empty($question->file_name)): ?> style="width: auto;"<?php endif; ?> for="question_recording_file_<?php if (empty($question)) echo 'QUESTION_ID'; else echo $question->getUniqueId(); ?>">
    
    <svg <?php if (!empty($question->file_name)): ?> style="display: none"<?php endif; ?> xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg>

    <?php if(empty($question) || empty($question->file_name)){ 
    ?>
    <span class="questioninputfile" > Select Question Recording </span>
    <?php 
    } 
    ?>

    <?php if(!empty($question->file_name)){ 
    ?>
    <span class="questioninputfile" > Already Selected Question File : <?= $question->file_name; ?> </span>
    <?php
    }
    ?>


    </label>
    <input style="display: none;" type="checkbox" id="file_updated_<?php if (empty($question)) {echo 'QUESTION_ID';} else {echo $question->getUniqueId();} ?>" name="file_updated[<?php if (empty($question)) {echo 'QUESTION_ID';} else {echo $question->getUniqueId();} ?>]" value="0" />
  </div>


  <div class="choices_container"<?php if (empty($question) || !in_array($question->question_type, array('radio'))): ?> style="display: none"<?php endif; ?>>
    <h4>Choices</h4>
    <div class="choices" data-question_id="<?php if (empty($question)) echo 'QUESTION_ID'; else echo $question->getUniqueId(); ?>">
      <?php if (!empty($question->choices)): ?>
      <?php foreach ($question->choices as $j => $choice): ?>
      <?php include 'survey_edit_choice.php'; ?>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div style="margin-top: 15px">
      <button class="add_choice">Add Choice</button>
    </div>
  </div>
</div>
<script type="text/javascript" src="js/custom-file-input.js"></script>
