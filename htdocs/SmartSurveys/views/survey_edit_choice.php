<div class="choice" data-choice_id="<?php if (empty($choice)) echo 'CHOICE_ID'; else echo $choice->getUniqueId(); ?>" data-choice_number="<?php if (isset($j)) echo $j + 1; ?>">
  	<label>Choice <span class="choice_number"><?php if (isset($j)) echo $j + 1; ?></span>:</label>
  	<input type="text" class="choice_text" name="choice_text[<?php if (empty($question)) echo 'QUESTION_ID'; else echo $question->getUniqueId(); ?>][<?php if (empty($choice)) echo 'CHOICE_ID'; else echo $choice->getUniqueId(); ?>]" value="<?php if (!empty($choice)) echo htmlspecialchars($choice->choice_text); ?>" placeholder="Upload Recording of Choice and enter it's corresponding text here"/>
 	
    <div class="recording_container">
    <input type="file" class="inputfilechoice" name="choice_recording_file[]" id="choice_recording_file_<?php if (empty($choice)) echo 'CHOICE_ID'; else echo $choice->getUniqueId(); ?>" data-multiple-caption="{count} files selected">
    <label <?php if (!empty($choice->file_name)): ?> onMouseOver="this.style.backgroundColor='#4b0f31'" onMouseOut="this.style.backgroundColor='#885365'" style="background-color: #885365;width: auto;"<?php endif; ?> <?php if (empty($choice) || empty($choice->file_name)): ?> style="width: auto;"<?php endif; ?> for="choice_recording_file_<?php if (empty($choice)) echo 'CHOICE_ID'; else echo $choice->getUniqueId(); ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg>

    <?php if(empty($choice) || empty($choice->file_name)){ 
    ?>
    <span class="choiceinputfile" > Select Choice Recording </span>
    <?php 
    } 
    ?>

    <?php if(!empty($choice->file_name)){ 
    ?>
    <span class="choiceinputfile" > Already Selected Choice File : <?= $choice->file_name; ?> </span>
    <?php
    }
    ?>


    </label>
    <input style="display: none;" type="checkbox" id="choice_file_updated_<?php if (empty($choice)) {echo 'CHOICE_ID';} else {echo $choice->getUniqueId();} ?>" name="choice_file_updated[<?php if (empty($question)) echo 'QUESTION_ID'; else echo $question->getUniqueId(); ?>][<?php if (empty($choice)) {echo 'CHOICE_ID';} else {echo $choice->getUniqueId();} ?>]" value="0" />
  </div>
  	
  	<button style="float: right;margin: -20px;" class="delete_choice">Delete Choice</button>
</div>
<script type="text/javascript" src="js/custom-file-input-choice.js"></script>
