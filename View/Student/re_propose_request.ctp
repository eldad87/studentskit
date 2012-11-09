<?php
if(isSet($paymentPage)) {
    echo '<p><strong>'.__('In order for those changes to take effect, please continue through the ').'</strong>'.$this->Html->link(__('Order page'), $paymentPage).'</p>';
} else {
?>
    <script type="text/javascript">
        $(document).ready(function() {
            <?php
                if(isSet($success)) {
                    echo '$(\'#negotiate-popup\').modal(\'hide\');';
                } else if($lessonType==LESSON_TYPE_LIVE) {
                    echo 'initSubjectForm(\'#UserLesson1On1Price\', \'#UserLessonLessonType\',
                                            \'#UserLessonMaxStudents\', \'#msDiv\',
                                            \'#fgspDiv\', \'#UserLessonFullGroupStudentPrice\');';
                }


                if(isSet($updateTooltip)) {
                    echo '$(\''.$updateTooltip.'\').data(\'title\',"'.$this->Layout->buildLessonTooltipHtml($userLessonData, $lessonType).'").tooltip(\'destroy\').tooltip({html: true});';
                }

                if(isSet($removeElement)) {
                    echo '$(\''.$removeElement.'\').hide();';
                }
            ?>
        });
    </script>
    <?php


    $this->Form->create('UserLesson');

        echo '<fieldset>';
            echo $this->Form->hidden('user_lesson_id');

            //Only for live lessons
            if($lessonType==LESSON_TYPE_LIVE) {
                echo $this->Form->input('datetime', $this->Layout->styleForInput(array('type'=>'datetime', 'class'=>false)));
                echo $this->Form->input('is_public', $this->Layout->styleForInput(array('options'=>array(SUBJECT_IS_PUBLIC_TRUE=>__('Yes'), SUBJECT_IS_PUBLIC_FALSE=>__('No')))));
                echo $this->Form->input('duration_minutes', $this->Layout->styleForInput(array('type'=>'number', 'min'=>10)));
            }
            echo $this->Form->input('lesson_type', $this->Layout->styleForInput(array('type'=>'hidden')));


            echo $this->Form->input('1_on_1_price', $this->Layout->styleForInput(array('type'=>'number', 'min'=>0)));

            //Only for live lessons
            if($lessonType==LESSON_TYPE_LIVE) {
                echo $this->Form->input('max_students', $this->Layout->styleForInput(array('type'=>'number', 'min'=>1, 'div'=>array('id'=>'msDiv', 'class'=>'control-group'))));
                echo $this->Form->input('full_group_student_price', $this->Layout->styleForInput(array( 'type'=>'number', 'min'=>0,
                                                                                                        'div'=>array('style'=>'display:none', 'id'=>'fgspDiv', 'class'=>'control-group') ,
                                                                                                        'tooltip'=>__('a max discount price for a full lesson, the discount will take place starting from 2 students and above'))));
            }

        echo '</fieldset>';
    $this->Form->end();
}
?>