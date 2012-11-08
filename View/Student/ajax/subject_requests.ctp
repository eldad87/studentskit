<p class="fontsize1 space8"><?php echo __('Here you can find lesson requests.'); ?></p>
<?php
    echo $this->Layout->lessonRequestButton(array('name'=>__('ADD'), 'class'=>'black-cent-butn2 add-blckbtn fontsize1 move-right'));
    echo $this->element('Home/lesson_request');
?>
<div class="add-sub pull-left space3">

    <?php
    foreach($response['response']['subjectRequests'] AS $subjectRequest) {
        /*
        echo 'Message: ',$upcomingLesson['UserLesson']['teacher_user_id'],'-',$upcomingLesson['UserLesson']['student_user_id'],'-',$upcomingLesson['UserLesson']['user_lesson_id'],'<br />';
        echo $this->Html->link('Cancel', array('controller'=>'Student','action'=>'cancelUserLesson', $upcomingLesson['UserLesson']['user_lesson_id']));
        */

        echo '<div class="lesson-box space2">
                <div class="head-back radius1">
                    <h1><strong>'.$subjectRequest['Subject']['name'].'</strong></h1>
                    <a href="#" class=" pull-right"><i class="iconMedium-add-del"></i></a>
                    <a href="#" class="pull-right"><i class="iconSmall-pencil  bot-pad"></i></a>
                </div>
                <div class="lesson-box-content">
                    <div class="user-pic2">'.$this->Html->image($this->Layout->image($subjectRequest['Subject']['image_source'], 72, 72), array('alt' => 'Lesson image'/*, 'class'=>'border1'*/)).'</div>
                    <div class="usr-text2">
                        <p>'.$subjectRequest['Subject']['description'].'</p>
                        <p class="space23">'.$this->Html->link(__('To the subject'), array('controller'=>'Home',
                                                                                                        'action'=>'subjectRequest',
                                                                                                        $subjectRequest['Subject']['subject_id'])).'</p>
                    </div>
                </div>
                <div class="lesson-box-footer radius2">

                    <div class="pull-right space21 right-i-mar">
                        '.$this->Layout->priceTag($subjectRequest['Subject']['1_on_1_price'], $subjectRequest['Subject']['full_group_student_price'], 'price-tag-panel').'
                        <!-- <a href="#" class=" pull-right space23"><i class="iconSmall-info r-mor-none"></i></a> -->
                    </div>
                </div> <!-- /lesson-box-footer -->
            </div> <!-- /lesson-box -->';
    }
    ?>

</div>