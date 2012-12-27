<script type="text/javascript">
    $(document).ready(function() {

        <?php
        if($subjectId && isSet($enableNextStep)) {
            //Add subject id to the links
            echo "$('.load3').each(function(){
                        var rel = $(this).attr('rel')
                        $(this).attr('rel', rel + '/' + $subjectId);
                    });";

            //Enable the meeting tab
            echo "$('#meetingTab').removeClass('disable');";

            echo 'initTabs();';
            echo '$(\'#meetingTab a\').click();';
        }
        ?>





        pfObj.loadForm('#subject-form', '#sub-area', 'post');

        initSubjectForm('#Subject1On1Price', '#SubjectLessonType',
            '#SubjectMaxStudents', '#msDiv',
            '#fgspDiv', '#SubjectFullGroupStudentPrice', '#durationDiv');

        initToolTips();
    });
</script>

<div class="cont-span6 cbox-space">
    <fieldset>
        <?php
        echo $this->Form->create( 'Subject',
                                    array(  'class'=>'sk-form', 'type' => 'file', 'method'=>'post', 'id'=>'subject-form',
                                            'url'=>array('controller'=>'Teacher', 'action'=>'subject', $subjectId)));
        
        echo $this->Form->input('name', $this->Layout->styleForInput());

        if(!$subjectId) {
            echo $this->Form->input('next', $this->Layout->styleForInput(array('type'=>'hidden', 'value'=>1)));
        }

        echo $this->Form->input('description', $this->Layout->styleForInput(array('type'=>'textarea')));
        echo $this->Form->input('subject_category_id', $this->Layout->styleForInput(array('options'=>$subjectCategories)));

        echo $this->Form->input('language', $this->Layout->styleForInput(array('options'=>$languages)));
        echo $this->Form->input('lesson_type', $this->Layout->styleForInput(array('options'=>array(LESSON_TYPE_LIVE=>__('Live'), LESSON_TYPE_VIDEO=>__('Video')))));
        //echo $this->Form->input('is_public', $this->Layout->styleForInput(array('options'=>array(SUBJECT_IS_PUBLIC_TRUE=>__('Yes'), SUBJECT_IS_PUBLIC_FALSE=>__('No')))));
        echo $this->Form->input('duration_minutes', $this->Layout->styleForInput(array('type'=>'number', 'min'=>10, 'div'=>array('id'=>'durationDiv', 'class'=>'control-group'))));
        echo $this->Form->input('imageUpload', $this->Layout->styleForInput(array('type'=>'file')));
        echo $this->Form->input('videoUpload', $this->Layout->styleForInput(array('type'=>'file')));

        echo $this->Form->input('1_on_1_price', $this->Layout->styleForInput(array('type'=>'number', 'min'=>0)));
        echo $this->Form->input('max_students', $this->Layout->styleForInput(array('type'=>'number', 'min'=>1, 'div'=>array('id'=>'msDiv', 'class'=>'control-group'))));

        echo $this->Form->input('full_group_student_price', $this->Layout->styleForInput(array( 'type'=>'number', 'min'=>0,
                                                                                                'div'=>array('style'=>'display:none', 'id'=>'fgspDiv', 'class'=>'control-group') ,
                                                                                                'tooltip'=>__('a max discount price for a full lesson, the discount will take place starting from 2 students and above'))));

        ?>
        <div class="control-group control2">
            <label class="control-label"></label>
            <div class="control">
                <button class="btn-blue pull-right" type="Save"><?php echo ($creationStage==CREATION_STAGE_NEW ? __('Save &amp; Next') : __('Save')); ?></button>
            </div>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </fieldset>
</div>