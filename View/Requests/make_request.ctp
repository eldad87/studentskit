<script type="text/javascript">
    $(document).ready(function() {

        initSubjectForm('#Subject1On1Price', '#SubjectLessonType',
                        '#SubjectMaxStudents', '#msDiv',
                        '#fgspDiv', '#SubjectFullGroupStudentPrice', '#durationDiv');
        <?php
            if(isSet($success)) {
                echo '$(\'#subject-request-popup\').modal(\'hide\');';

                if(isSet($appendTemplate) && isSet($subjectRequestData)) {

                    $appendTemplate = ($appendTemplate=='user-panel' ? 'Panel/user_subject_request_div' : '');

                    if(isSet($prependTo)) {
                        echo '$(\'#',$prependTo,'\').prepend(\'', $this->Layout->stringToJSVar($this->element($appendTemplate, array('subjectRequestData'=>$subjectRequestData))) , '\');';
                    } else if(isSet($replaceWith)) {
                        //Change subject request box
                        echo '$(\'#',$replaceWith,'\').replaceWith(\'', $this->Layout->stringToJSVar($this->element($appendTemplate, array('subjectRequestData'=>$subjectRequestData))) , '\');';
                    }

                    echo 'initToolTips();';
                }
            }
        ?>

    });
</script>
<fieldset>
    <?php
    $this->Form->create('Subject');
    //echo preg_replace("/<form[^>]+\>/i", "", $form); //Remove <form>

    //echo $this->Form->hidden('subject_id');
    echo $this->Form->input('name', $this->Layout->styleForInput());
    echo $this->Form->input('description', $this->Layout->styleForInput(array('type'=>'textarea')));
    //echo $this->Form->input('subject_category_id', $this->Layout->styleForInput(array('options'=>$subjectCategories)));
    echo $this->Form->input('subject_category_id', $this->Layout->styleForInput(array('options'=>$subjectCategories)));

    echo $this->Form->input('language', $this->Layout->styleForInput(array('options'=>$languages)));
    echo $this->Form->input('lesson_type', $this->Layout->styleForInput(array('options'=>array(LESSON_TYPE_LIVE=>__('Live'), LESSON_TYPE_VIDEO=>__('Video')))));
    echo $this->Form->input('is_public', $this->Layout->styleForInput(array('options'=>array(SUBJECT_IS_PUBLIC_TRUE=>__('Yes'), SUBJECT_IS_PUBLIC_FALSE=>__('No')))));
    echo $this->Form->input('duration_minutes', $this->Layout->styleForInput(array('type'=>'number', 'min'=>4, 'div'=>array('id'=>'durationDiv', 'class'=>'control-group'))));
    echo $this->Form->input('imageUpload', $this->Layout->styleForInput(array('type'=>'file')));

    echo $this->Form->input('1_on_1_price', $this->Layout->styleForInput(array('type'=>'number', 'min'=>0, 'step'=>'any')));
    echo $this->Form->input('max_students', $this->Layout->styleForInput(array('type'=>'number', 'min'=>1, 'div'=>array('id'=>'msDiv', 'class'=>'control-group'))));

    echo $this->Form->input('full_group_student_price', $this->Layout->styleForInput(array( 'type'=>'number', 'min'=>0, 'step'=>'any',
                                                                                            'div'=>array('style'=>'display:none', 'id'=>'fgspDiv', 'class'=>'control-group') ,
                                                                                            'tooltip'=>__('a max discount price for a full lesson, the discount will take place starting from 2 students and above'))));
    echo $this->Form->end();

    //echo preg_replace("/</form[^>]+\>/i", "", $endForm); //Remove <form>
    ?>
</fieldset>