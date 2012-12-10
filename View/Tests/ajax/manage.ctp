<script type="text/javascript">


    $(document).ready(function() {
        $('#quiz-builder').quizBuilder({
            save: {
                buttonSelector: '.save-quiz',
                errorSelector: '#quiz-builder',
                fields: [
                    'name',
                    'description',
                    'subject_id'
                ]
            },
            data: jQuery.parseJSON('<?php echo $testData['questions']; ?>')
        });
        initTabs(false);
    });

</script>
<?php
/*echo $this->Html->link('reload',
    '#',
    array( 'class'=>'pull-left load3',
        'rel'=>Router::url(array('controller'=>'Tests', 'action'=>'manage', $subjectId))));*/
?>
<br />
<div class="cont-span19 cbox-space">
    <div id="quiz-builder">

        <form class="sk-form">
            <input type="hidden" name="subject_id" id="subject_id" value="<?php echo $subjectId; ?>" />
            <fieldset>
                <!--Name-->
                <div class="control-group">
                    <label class="control-label" for="name"><?php echo __('Name') ?> :</label>
                    <div class="control">
                        <input type="text" class="x-large2" name="name" value="<?php echo $testData['name']; ?>" id="name">
                    </div>
                </div>

                <!--Description-->
                <div class="control-group">
                    <label class="control-label" for="description"><?php echo __('Description') ?> :</label>
                    <div class="control">
                        <textarea type="text" class="x-large2" rows="3" name="description" id="description"><?php echo $testData['description']; ?></textarea>
                    </div>
                </div>


                <!--Question list-->
                <ul class="question-list space32">
                    <!--Question block-->
                    <li class="question-block" id="question_block_question_id">
                        <!--Select right answer-->
                        <div class="control-group" id="right_answer_block_question_id">
                            <label class="control-label" for="right_answer_select_question_id"><?php echo __('Right Answer :'); ?></label>
                            <div class="control">
                                <select class="in-large-2" name="right_answer_name" id="right_answer_select_question_id"></select>
                            </div>
                        </div>

                        <!--Question-->
                        <div class="control-group" id="question_question_id">
                            <label class="control-label" for="question_text_question_id"><?php echo __('Question'); ?> #</label>
                            <div class="control">
                                <input type="text" class="x-large2 pull-left" name="question_name" value="" id="question_text_question_id" />
                                <a href="#"><i class="iconSmall-red-cross redcross actionButton delete-question" id="delete_question_question_id"></i></a>
                            </div>
                        </div>

                        <!--Answers-->
                        <ul class="answer-list" id="answer_list_question_id">
                            <!--Answer-->
                            <li class="answer-block" id="answer_block_answer_id">
                                <div class="control-group">
                                    <label class="control-label" for="answer_text_question_id_answer_id"><?php echo __('Answer'); ?> #</label>
                                    <div class="control a-black">
                                        <input type="text" class="x-large2 pull-left" name="answer_name" value="" id="answer_text_question_id_answer_id">
                                        <a href="#"><i class="iconSmall-red-cross redcross actionButton delete-answer" id="delete_answer_question_id_answer_id"></i></a>
                                    </div>
                                </div>
                            </li>
                        </ul>

                        <!--Add answer-->
                        <div class="control-group">
                            <label class="control-label"></label>
                            <div class="control">
                                <p class=" pull-left space19 a-black"><a class="add-icon2 add-answer" href="#"><i class="iconSmall-add-rang"></i><strong><?php echo __('Add Answer'); ?></strong></a></p>
                            </div>
                        </div>
                    </li>
                </ul>


                <!--Add question-->
                <div class="control-group space7">
                    <label class="control-label"></label>
                    <div class="control">
                        <p class="pull-left space19 a-black"><a class="add-icon2 add-question" href="#"><i class="iconSmall-add-rang"></i><strong><?php echo __('Add Question'); ?></strong></a></p>
                    </div>
                </div>
                <!--Save-->
                <div class="control-group">
                    <label class="control-label"></label>
                    <div class="control">
                        <button type="button" class="btn-blue save-quiz" data-target="<?php echo Router::url(array('controller'=>'Tests', 'action'=>'save', $testId)); ?>"><?php echo __('Save'); ?></button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div> <!-- /fullwidth -->
</div>