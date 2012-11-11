<p class="fontsize1 space8"><?php echo __('Here you can find all your past lessons.'); ?></p>

<?php
echo $this->element('panel/send_msg_popup', array('buttonSelector'=>'.msg-teacher'));
?>
<script type="text/javascript">
    $(document).ready(function(){
        //Activate tooltip
        initToolTips();
    });
</script>
<div class="add-sub pull-left space3">

    <?php
    foreach($response['response']['archiveLessons'] AS $archiveLessons) {

        $toTheLessonLink = false;

        //Lesson took place
        if(in_array($archiveLessons['UserLesson']['stage'], array(USER_LESSON_PENDING_RATING, USER_LESSON_PENDING_TEACHER_RATING,
                                                                    USER_LESSON_PENDING_STUDENT_RATING, USER_LESSON_DONE,
                                                                    USER_LESSON_ACCEPTED))) {
            $toTheLessonLink = $this->Html->link(__('Lesson page'), array('controller'=>'Lessons',
                                                        'action'=>($archiveLessons['UserLesson']['lesson_type']==LESSON_TYPE_LIVE ? 'index' : 'video'),
                                                        $archiveLessons['UserLesson']['teacher_lesson_id']));
        }

        echo '<div class="lesson-box space2">
                <div class="head-back radius1">
                    <h1>'.$this->Time->niceShort($archiveLessons['UserLesson']['datetime']).' -  <strong>'.$archiveLessons['UserLesson']['name'].'</strong></h1>
                    <div class="dropdown pull-right">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                            <i class="iconSmall-drop-arrow"></i>
                        </a>
                        <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                            <li>'.$this->Html->link(__('Download Receipt'), array('controller'=>'Billing', 'action'=>'downloadReceipt', $archiveLessons['UserLesson']['user_lesson_id'])).'</li>
                            <li><a href="#" class="msg-teacher" data-entity_type="lesson" data-entity_id="'.$archiveLessons['UserLesson']['user_lesson_id'].'" data-to_user_id="'.$archiveLessons['UserLesson']['teacher_user_id'].'">'.__('Message teacher').'</a></li>
                            '.($toTheLessonLink ? '<li>'.$toTheLessonLink.'</li>' : null).'
                        </ul>
                    </div>
                </div>
                <div class="lesson-box-content">
                    <div class="user-pic2">'.$this->Html->image($this->Layout->image($archiveLessons['UserLesson']['image_source'], 72, 72), array('alt' => 'Lesson image'/*, 'class'=>'border1'*/)).'</div>
                    <div class="usr-text2">
                        <h4>'.__('by').' '.$this->Html->link($archiveLessons['Teacher']['username'], array('controller'=>'Home', 'action'=>'teacher', $archiveLessons['UserLesson']['teacher_user_id'])).'</h4>
                        <p>'.$archiveLessons['UserLesson']['description'].'</p>
                        '.($toTheLessonLink ? '<p class="space23">'.$toTheLessonLink.'</p>' : null).'
                    </div>
                </div>
                <div class="lesson-box-footer radius2">
                    <div class="pull-left star">
                        '.$this->Html->image($this->Layout->rating($archiveLessons['UserLesson']['rating_by_student'], false), array('alt' => 'User rating')).'
                    </div>
                    <div class="pull-right space21 right-i-mar">
                        '.$this->Layout->toolTip($this->Layout->buildLessonTooltipHtml(am($archiveLessons['TeacherLesson'], $archiveLessons['UserLesson'])), null, 'pull-right space23', 'tooltip_'.$archiveLessons['UserLesson']['user_lesson_id']).'
                        '.$this->Layout->priceTag($archiveLessons['UserLesson']['1_on_1_price'], $archiveLessons['UserLesson']['full_group_student_price'], 'price-tag-panel').'
                    </div>
                </div> <!-- /lesson-box-footer -->
            </div> <!-- /lesson-box -->';
    }
    ?>
</div>
