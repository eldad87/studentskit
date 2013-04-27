
<?php
if(isSet($paymentPage)) {
    echo '<p><strong>'.sprintf(__('In order for those changes to take effect, additional payment (%0.2f%s) is require; please continue through the'), $paymentShortAmount, 'USD').'</strong> '.$this->Html->link(__('Order page'), $paymentPage).'</p>';
} else {
?>
    <script type="text/javascript">
        $(document).ready(function() {
            <?php


                if(isSet($creditPoints)) {
                    echo '$(\'#creditPointsCounter\').html('.$creditPoints.');';
                }

                if(isSet($success)) {
                    echo '$(\'#negotiate-popup\').modal(\'hide\');';
                } else if(isSet($error)) {
                    echo 'showError(\'#accept-popup .modal-body\',\''.__('Internal Error').'\', \'\')';
                } else if($lessonType==LESSON_TYPE_LIVE) {
                    /*echo 'initSubjectForm(\'#UserLessonPrice\', \'#UserLessonLessonType\',
                                            \'#UserLessonMaxStudents\', \'#msDiv\',
                                            \'#fgspDiv\', \'#UserLessonFullGroupStudentPrice\', \'#durationDiv\');';*/

                    echo 'initSubjectAddForm(
                            \'#UserLessonIsPublic\',
                            \'#UserLessonLessonType\',
                            \'#UserLessonPrice\',
                            \'#UserLessonMaxStudents\'
                    );';
                }


                /*if(isSet($updateTooltip)) {
                    echo '$(\''.$updateTooltip.'\').data(\'title\',"'.$this->Layout->buildLessonTooltipHtml($userLessonData, $lessonType).'").tooltip(\'destroy\').tooltip({html: true});';
                }*/

                if(isSet($updateLessonBoxAfterNegotiate)) {
                    //Change lesson box
                    echo '$(\'#',$updateLessonBoxAfterNegotiate['element'],'\').replaceWith(\'',
                                                                                    $this->Layout->stringToJSVar(
                                                                                        $this->element('Panel'.DS.'lesson_box_li',
                                                                                            array('lessonData'=>$updateLessonBoxAfterNegotiate['data'],
                                                                                            'id'=>$updateLessonBoxAfterNegotiate['element'])))
                        ,   '\');';

                    //Change message
                    echo '$(\'#',$updateLessonBoxAfterNegotiate['element'],'_msg\').html(\'',$this->Layout->stringToJSVar($updateLessonBoxAfterNegotiate['data']['UserLesson']['offer_message']),'\');';

                    //Init tooltip
                    echo 'initToolTips()';
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

            echo $this->Form->input('offer_message', $this->Layout->styleForInput());

            //Only for live lessons
            if($lessonType==LESSON_TYPE_LIVE) {
                echo $this->Form->input('datetime', $this->Layout->styleForInput(array('type'=>'datetime', 'class'=>false)));
                echo $this->Form->input('is_public', $this->Layout->styleForInput(array('options'=>array(SUBJECT_IS_PUBLIC_TRUE=>__('Yes'), SUBJECT_IS_PUBLIC_FALSE=>__('No')))));
                echo $this->Form->input('duration_minutes', $this->Layout->styleForInput(array('type'=>'number', 'min'=>4, 'div'=>array('id'=>'durationDiv', 'class'=>'control-group'))));
            }
            echo $this->Form->input('lesson_type', $this->Layout->styleForInput(array('type'=>'hidden')));




            echo $this->Form->input('price', $this->Layout->styleForInput(array('type'=>'number', 'min'=>0, 'step'=>'any')));


            //Only for live lessons
            if($lessonType==LESSON_TYPE_LIVE) {
                echo '<div id="maxStudentsAndDiscountDiv">';

                echo $this->Form->input('max_students', $this->Layout->styleForInput(array('type'=>'number', 'min'=>1, 'div'=>array(/*'id'=>'msDiv', */'class'=>'control-group'))));
                /*echo $this->Form->input('full_group_student_price', $this->Layout->styleForInput(array( 'type'=>'number', 'min'=>0, 'step'=>'any',
                                                                                                        'div'=>array('style'=>'display:none', 'id'=>'fgspDiv', 'class'=>'control-group') ,
                                                                                                        'tooltip'=>__('a max discount price for a full lesson, the discount will take place starting from 2 students and above'))));*/
                echo $this->Form->input('full_group_student_price', $this->Layout->styleForInput(array( 'type'=>'number', 'min'=>0, 'step'=>'any',
                                                                                                        'label'=>array('class'=>'control-label', 'text'=>__('Volume Discount')),
                                                                                                        'div'=>array('style'=>'display:none', 'id'=>'discountPriceDiv', 'class'=>'control-group') ,
                                                                                                        'tooltip'=>__('The max discount for when the lesson is full. The discount is relative to the amount of students and will take affect starting from the 2nd student. Leave BLANK for no discount.'),
                                                                                                        'tooltip_class'=>'pull-right space3'
                                                                                                    )));

                echo '</div>';
            }

        echo '</fieldset>';
    $this->Form->end();
}
?>