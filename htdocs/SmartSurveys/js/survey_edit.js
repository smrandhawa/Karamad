var question_id_counter = 1;
var choice_id_counter = 1;
var condition_id_counter = 1;
var question_condition_id_counter = 1;
/**
 * Get a new condition id
 *
 * @return string condition_id
 */
function getUniqueQuestionConditionId()
{
    return 'QuestionCondition' + question_condition_id_counter++;
}
/**
 * Get a new condition id
 *
 * @return string condition_id
 */
function getUniqueConditionId()
{
    return 'Condition' + condition_id_counter++;
}

/**
 * Get a new question id to associate choice ids with this question id
 *
 * @return string question_id
 */
function getUniqueQuestionId()
{
    return 'Question' + question_id_counter++;
}

/**
 * Get a new choice id
 *
 * @return string choice_id
 */
function getUniqueChoiceId()
{
    return 'Choice' + choice_id_counter++;
}

/**
 * Check if warning text exists
 *
 * @return true/false
 */
function checkWarningLabelExists()
{
    if ($('label[name="empty_surveys_warning"]').length)
    {
        return true;
    }
    return false;
}

/**
 * animate the warning text label
 *
 */
function animateWarning()
{
    if (checkWarningLabelExists())
    {
        $('label[name="empty_surveys_warning"]').toggleClass('anim');

        const sleep = (milliseconds) => {
            return new Promise(resolve => setTimeout(resolve, milliseconds))
        }

        const doSomething = async () => {
        await sleep(1000);
        $('label[name="empty_surveys_warning"]').toggleClass('anim');
        }

        doSomething();
    }
}

/**
 * Add a new condition to the survey
 */
function addCondition()
{

    var conditions = $(this).parents('.conditions_container').find('.conditions');
    if (checkWarningLabelExists() == false)
    {   
        var conditionID = getUniqueConditionId();
        var newConditionHtml = conditionHtml.replace(/CONDITION_ID/g, conditionID);
        conditions.append(newConditionHtml);

        initDeleteConditionButton('.delete_condition');
    }
    animateWarning();  
    $('#add_condition').blur();

    return false;
}

/**
 * Add a new condition to the survey
 */
function addQuestionCondition()
{

    var questionconditions = $(this).parents('.conditions_container').find('.questionconditions');
    if (checkWarningLabelExists() == false)
    {
        var questionconditionID = getUniqueQuestionConditionId();
        var newQuestionConditionHtml = questionconditionHtml.replace(/QUESTION_CONDITION_ID/g, questionconditionID);
        questionconditions.append(newQuestionConditionHtml);
        
        initDeleteQuestionConditionButton('.delete_question_condition');
    }
    animateWarning();  
    $('#add_question_condition').blur();

    return false;
}

/**
 * Add a new choice to a question
 */
function addChoice()
{
    var choices = $(this).parents('.question').find('.choices');
    var questionID = $(this).parents('.question').data('question_id');
    var choiceID = getUniqueChoiceId();
    var newChoiceHtml = choiceHtml.replace(/QUESTION_ID/g, questionID).replace(/CHOICE_ID/g, choiceID);
    choices.append(newChoiceHtml);

    initDeleteChoiceButton('div.choice[data-choice_id="' + choiceID + '"] button.delete_choice');

    renumberChoices(questionID);

    // Focus on new choice text box
    $('div.choice[data-choice_id="' + choiceID + '"] input.choice_text').focus();

    return false;
}

/**
 * Add a new question to the survey
 */
function addQuestion()
{
    var questions = $(this).parents('.questions_container').find('.questions');
    var questionID = getUniqueQuestionId();
    var newQuestionHtml = questionHtml.replace(/QUESTION_ID/g, questionID);
    questions.append(newQuestionHtml);


    initQuestionTypeDropDown('div[data-question_id="' + questionID + '"] select.question_type');
    initQuestionGenericCheckbox('div[data-question_id="' + questionID + '"] div.generic_question_multiple_choice_derived_container');
    initSortableChoices('div.choices[data-question_id="' + questionID + '"]');
    initAddChoiceButton('div[data-question_id="' + questionID + '"] button.add_choice');
    initDeleteQuestionButton('div[data-question_id="' + questionID + '"] button.delete_question');
    initMoveQuestionUpButton('div[data-question_id="' + questionID + '"] button.move_question_up');
    initMoveQuestionDownButton('div[data-question_id="' + questionID + '"] button.move_question_down');

    renumberQuestions();
    //$('div[data-question_id="' + questionID + '"]').children('.generic_question_multiple_choice_derived_container').hide();

    // Focus on new question text box
    $('div[data-question_id="' + questionID + '"] input.question_text').focus();

    return false;
}

/**
 * Delete the survey
 */
function deleteSurvey()
{
    $('#action').val('delete_survey');
    if (confirm("Are you sure you want to delete this survey?"))
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * Delete a condition from a survey
 */
function deleteCondition()
{
    
    $(this).parents('.condition').remove();
    return false;
}

/**
 * Delete a question condition from a survey
 */
function deleteQuestionCondition()
{
    
    $(this).parents('.question_condition').remove();
    return false;
}

/**
 * Delete a choice from a question
 */
function deleteChoice()
{
    var questionID = $(this).parents('.question').data('question_id');
    var choiceID = $(this).parents('div.choice').data('choice_id');
    $('div.choice[data-choice_id="' + choiceID + '"]').remove();

    renumberChoices(questionID);

    return false;
}

/**
 * Delete a question from the survey
 */
function deleteQuestion()
{
    $(this).parents('.question').remove();

    renumberQuestions();

    return false;
}

/**
 * Renumber the questions
 */
function renumberQuestions()
{
    $('div.question').each(function(i, obj)
    {
        $(obj).find('.question_number').text(i + 1);
    });
}

/**
 * Move a question up
 */
function moveQuestionUp()
{
    
    var question = $(this).parents('div.question');

    if (question.prev().length > 0)
    {
        var old_num = question.find('.question_number').text();
        question.prev().before(question);
        renumberQuestions();
        if (old_num == question.find('.question_number').text())
        {
            question.prev().before(question);
        }
    }
    renumberQuestions();

    return false;
}

/**
 * Move a question down
 */
function moveQuestionDown()
{
    renumberQuestions();
    var question = $(this).parents('div.question');
    if (question.next().length > 0)
    {
        var old_num = question.find('.question_number').text();
        question.next().after(question);
        renumberQuestions();
        if (old_num == question.find('.question_number').text())
        {
            question.next().after(question);
        }
        
    }
    renumberQuestions();

    return false;
}

/**
 * Renumber a question's choices
 */
function renumberChoices(questionID)
{
    $('div.question[data-question_id="' + questionID + '"]').find('div.choice').each(function(i, obj)
    {
        $(obj).find('.choice_number').text(i + 1);
    });
}


/**
 * Handle a question_type change, hide the choices if it is an open text field
 */
function questionTypeChange()
{
    var showChoices = false;

    switch ($(this).val())
    {
        case 'radio':
            showChoices = true;
            break;
    }

    var choices = $(this).parents('.question').find('.choices_container');
    var gen_check_box = $(this).parents('.question').find('.generic_question_multiple_choice_derived_container');
    if (showChoices)
    {
        choices.show();
        gen_check_box.show();
    }
    else
    {
        choices.hide();
        gen_check_box.children('div.generic_question_multiple_choice_derived_checkbox').find('input').prop('checked', false);
        gen_check_box.hide();
        
    }
}

/**
 * Make the choices sortable and renumber the choices when a choice is re-sorrted
 */
function initSortableChoices(selector)
{
    $(selector).sortable({
        disable: true,
        placeholder: "ui-state-highlight",
        items: 'div.choice',
        stop: function(event, ui)
        {
            renumberChoices($(this).parents('div.question').data('question_id'));
        }
    });
}

/**
 * Create a jQuery UI add choice button
 */
function initAddChoiceButton(selector)
{
    $(selector).button({ icons: { primary: 'ui-icon-plusthick' } }).on('click', addChoice);
}

/**
 * Create a jQuery UI delete choice button and delete the choice when clicked
 */
function initDeleteChoiceButton(selector)
{
    $(selector).button({ icons: { primary: 'ui-icon-trash' } }).on('click', deleteChoice);
}

/**
 * Create a jQuery UI delete question button and delete the question when clicked
 */
function initDeleteQuestionButton(selector)
{
    $(selector).button({ icons: { primary: 'ui-icon-trash' } }).on('click', deleteQuestion);
}

/**
 * Create a jQuery UI button that moves the question up when clicked
 */
function initMoveQuestionUpButton(selector)
{
    $(selector).button().on('click', moveQuestionUp);
}

/**
 * Create a jQuery UI button that moves the question down when clicked
 */
function initMoveQuestionDownButton(selector)
{
    $(selector).button().on('click', moveQuestionDown);
}



/**
 * Handle when the question dependence container
 */
function initQuestionGenericCheckbox(selector)
{
    $(selector).each(function(i, obj){
        var question_type = $(obj).parents('div.question').find('select.question_type').val();
        //var c = $(obj).children('div.dependent_checkbox').find('input').val();
        //alert(qno);
        if(question_type == 'input')
        { 
            $(obj).children('div.generic_question_multiple_choice_derived_checkbox').find('input').prop('checked', false); // Unchecks it
            $(obj).hide();
        }
    });
}

/**
 * Handle when the question dependence container
 */
function initQuestionDependence(selector)
{
    $(selector).each(function(i, obj){
        var qno = $(obj).parents('div.question').find('.question_number').text();
        //var c = $(obj).children('div.dependent_checkbox').find('input').val();
        //alert(qno);
        if(qno == 1)
        { 
            $(obj).children('div.dependent_checkbox').find('input').prop('checked', false); // Unchecks it
            $(obj).hide();
        }
    });
}

/**
 * Handle when the question type is changed
 */
function initQuestionTypeDropDown(selector)
{
    $(selector).on('keyup change', questionTypeChange);
}

/**
 * Create a jQuery UI delete condition button and delete the condition when clicked
 */
function initDeleteConditionButton(selector)
{
    $(selector).button({ icons: { primary: 'ui-icon-trash' } }).on('click', deleteCondition);
}

/**
 * Create a jQuery UI delete question condition button and delete the condition when clicked
 */
function initDeleteQuestionConditionButton(selector)
{
    $(selector).button({ icons: { primary: 'ui-icon-trash' } }).on('click', deleteQuestionCondition);
}

/**
 * Validate the condition to ensure conditions are selected if condition selection boxes are added and also
 * ensure these conditions are unique too
 */
function validateCondition()
{
    var validate = true;

    var selected_surveys_ids = [];
    $('.select_condition').each(function(i, obj)
    {
        var selected_survey_in_condition = obj.options[obj.selectedIndex].value;
        if (selected_survey_in_condition == "-")
        {
            var error = "Please, select survey task for the condition.";
            alert(error);
            obj.focus();
            validate = false;
            return false;
        }

        if ($.inArray(selected_survey_in_condition, selected_surveys_ids) < 0) 
        {
            selected_surveys_ids.push(selected_survey_in_condition);
        }
        else
        {   
            var error = "You have already defined condition on ";
            error = error.concat(obj.options[obj.selectedIndex].text); 
            alert(error);
            obj.focus();
            validate = false;
        }
    });

    return validate;
}

/**
 * Validate the question conditions to ensure conditions are selected if question answered condition 
 * selection boxes are added and also ensure these questions conditions are unique too
 */
function validateQuestionCondition()
{   
    var validate = true;

    var selected_survey_question_choice_strings = [];
    var selected_survey_question_strings = [];
    $('.select_question_condition').each(function(i, obj)
    {
        var selected_survey_question_choice_string_in_condition = obj.options[obj.selectedIndex].value;
        if (selected_survey_question_choice_string_in_condition == "-")
        {
            var error = "Please, select survey question and answered choice for the condition.";
            alert(error);
            obj.focus();
            validate = false;
            return false;
        }

        if ($.inArray(selected_survey_question_choice_string_in_condition, selected_survey_question_choice_strings) < 0) 
        {
            selected_survey_question_choice_strings.push(selected_survey_question_choice_string_in_condition);
        }
        else
        {   
            var error = "You have already defined condition on this Question Choice pair : ";
            error = error.concat(obj.options[obj.selectedIndex].text); 
            alert(error);
            obj.focus();
            validate = false;
            return false;
        }

        var selected_survey_question_string_in_condition_array = selected_survey_question_choice_string_in_condition.split("_",4);
        var selected_survey_question_string_in_condition = selected_survey_question_string_in_condition_array[1] +' –– Question '+ selected_survey_question_string_in_condition_array[3];
        if ($.inArray(selected_survey_question_string_in_condition, selected_survey_question_strings) < 0) 
        {
           selected_survey_question_strings.push(selected_survey_question_string_in_condition);
        }
        else
        {   
            var error = "You have already defined condition on this Question : ";
            error = error.concat(selected_survey_question_string_in_condition); 
            alert(error);
            obj.focus();
            validate = false;
            return false;
        }

    });

    return validate;
}

/**
 * Validate the form to ensure required fields are filled in
 */
function validateForm()
{
    var validate = true;
    if ($('#survey_name').val().length == 0)
    {
        alert('Please enter the survey title');
        validate = false;
    }


    var valofquestionlabel =  '' ;
    $( ".choiceinputfile" ).each(function( i, obj ) {
        valofquestionlabel =  $(obj).text() ;
        var valueisdefault = valofquestionlabel.includes("Select Choice");
         if (valueisdefault)
         {
            var choicesdivstyle = $(obj).closest('div[class^="choices_container"]').css("display");
            
            //alert($(obj).closest('div[class^="choices_container"]').css("display"));

            if (choicesdivstyle != "none")
            {
                alert('Please select Recording Files for all Choices');
                validate = false;
                return false;
            } 
         }
    });


    var valofquestionlabel =  '' ;
    $( ".questioninputfile" ).each(function( i, obj ) {
        valofquestionlabel =  $(obj).text() ;
        var valueisdefault = valofquestionlabel.includes("Select Question");
        //alert(valofquestionlabel);

         if (valueisdefault)
         {
            alert('Please select Recording Files for all Questions');
            validate = false;
            return false;
         }
    });

    var valofdescritionlabel =  '' ;
    $( ".textinputfilesurveydescription" ).each(function( i, obj ) {
        valofdescritionlabel =  $(obj).text() ;
        var valueisdefault = valofdescritionlabel.includes("Select Survey");
        //alert(valofquestionlabel);

         if (valueisdefault)
         {
            alert('Please select File for Survey Brief Description Recording');
            validate = false;
            return false;
         }
    });

    var valofinstructionlabel =  '' ;
    $( ".textinputfilesurveyinstructions" ).each(function( i, obj ) {
        valofinstructionlabel =  $(obj).text() ;
        var valueisdefault = valofinstructionlabel.includes("Select Instructions");
        //alert(valofquestionlabel);
        
         if (valueisdefault)
         {
            alert('Please select File for Instructions for Workers Recording');
            validate = false;
            return false;
         }
    });

    if(validate)
    {
        validate = validateCondition();
    }

    if(validate)
    {
        validate = validateQuestionCondition();
    }

    return validate;
}

function validateFormContinue()
{
    validate = validateForm();
    if(validate)
    {
        $('#action').val('save_continue');
    }
    return validate;
}

/**
 * Page load - initialize handlers
 */
$(function()
{
    $('#submitButton').button({ icons: { primary: 'ui-icon-disk' }}).click(validateForm);
    $('#delete_survey').button({ icons: { primary: 'ui-icon-closethick' }}).click(deleteSurvey);
    $('#save_continue').button({ icons: { primary: 'ui-icon-arrowthick-1-e' }}).click(validateFormContinue);
    $('#add_condition').button({ icons: { primary: 'ui-icon-plusthick' }}).click(addCondition);
    $('#add_question_condition').button({ icons: { primary: 'ui-icon-plusthick' }}).click(addQuestionCondition);
    $('#add_question').button({ icons: { primary: 'ui-icon-plusthick' }}).click(addQuestion);
        

    initQuestionTypeDropDown('select.question_type');
    initQuestionGenericCheckbox('.generic_question_multiple_choice_derived_container');
    initQuestionDependence('.dependent_container');
    initSortableChoices("div.choices");
    initAddChoiceButton('.add_choice');
    initDeleteChoiceButton('.delete_choice');
    initDeleteQuestionButton('.delete_question');
    initMoveQuestionUpButton('.move_question_up');
    initMoveQuestionDownButton('.move_question_down');
    initDeleteConditionButton('.delete_condition');
    initDeleteQuestionConditionButton('.delete_question_condition');
});
