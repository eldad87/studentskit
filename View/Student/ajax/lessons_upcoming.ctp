<p class="fontsize1 space8"><?php echo __('Here you can find all your future lessons.'); ?></p>

<?php
echo $this->element('panel/cancel_popup', array('buttonSelector'=>'.confirm-delete',
                                                        'title'=>__('Cancel your participation'),
                                                        'description'=>__('This procedure may be irreversible.
                                                            Do you want to proceed?'),
                                                        'cancelUrl'=>array('controller'=>'Student', 'action'=>'cancelUserLesson', '{id}')));
echo $this->element('panel/invite_popup', array('buttonSelector'=>'.invite'));
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
    foreach($response['response']['upcomingLessons'] AS $upcomingLesson) {
        $toTheLessonLink = $this->Html->link(__('Lesson page'), array('controller'=>'Lessons',
                                                                'action'=>($upcomingLesson['UserLesson']['lesson_type']==LESSON_TYPE_LIVE ? 'index' : 'video'),
                                                                $upcomingLesson['UserLesson']['teacher_lesson_id']));

        echo '<div class="lesson-box space2" id="user_lesson_id_'.$upcomingLesson['UserLesson']['user_lesson_id'].'">
                <div class="head-back radius1">
                    <h1>'.$this->Layout->lessonTypeIcon($upcomingLesson['UserLesson']['lesson_type']).
                        $this->Time->niceShort($upcomingLesson['UserLesson']['datetime']).' -  <strong>'.$upcomingLesson['UserLesson']['name'].'</strong></h1>
                    <div class="dropdown pull-right">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                            <i class="iconSmall-drop-arrow"></i>
                        </a>
                        <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                            <li><a href="#" class="msg-teacher" data-entity_type="user_lesson" data-entity_id="'.$upcomingLesson['UserLesson']['user_lesson_id'].'" data-to_user_id="'.$upcomingLesson['UserLesson']['teacher_user_id'].'">'.__('Message teacher').'</a></li>
                            <li><a href="#" class="invite" data-teacher_lesson_id="'.$upcomingLesson['UserLesson']['teacher_lesson_id'].'"> '.__('Invite friends').'</a></li>
                            <li>'.$toTheLessonLink.'</li>
                            <li><a href="#" class="confirm-delete" data-cancel-prefix="user_lesson_id" data-id="'.$upcomingLesson['UserLesson']['user_lesson_id'].'">'.__('Cancel').'</a></li>
                        </ul>
                    </div>



                </div>
                <div class="lesson-box-content">
                    <div class="user-pic2">'.$this->Html->image($this->Layout->image($upcomingLesson['UserLesson']['image_source'], 72, 72), array('alt' => 'Lesson image'/*, 'class'=>'border1'*/)).'</div>
                    <div class="usr-text2">
                        <h4>'.__('by').' '.$this->Html->link($upcomingLesson['Teacher']['username'], array('controller'=>'Home', 'action'=>'teacher', $upcomingLesson['UserLesson']['teacher_user_id'])).'</h4>
                        <p>'.$upcomingLesson['UserLesson']['description'].'</p>
                        <p class="space23">'.$toTheLessonLink.'</p>
                    </div>
                </div>
                <div class="lesson-box-footer radius2">
                    <div class="pull-left star">
                        '.$this->Layout->ratingNew($upcomingLesson['UserLesson']['rating_by_student'], false, 'pull-left pad8').'
                    </div>
                    <div class="pull-right space21 right-i-mar">
                        '.$this->Layout->toolTip($this->Layout->buildLessonTooltipHtml(am($upcomingLesson['TeacherLesson'], $upcomingLesson['UserLesson'])), null, 'pull-right space23', 'tooltip_'.$upcomingLesson['UserLesson']['user_lesson_id']).'
                        '.$this->Layout->priceTag($upcomingLesson['UserLesson']['1_on_1_price'], $upcomingLesson['UserLesson']['full_group_student_price'], 'price-tag-panel').'
                        <!-- <a href="#" class=" pull-right space23"><i class="iconSmall-info r-mor-none"></i></a> -->
                    </div>
                </div> <!-- /lesson-box-footer -->
            </div> <!-- /lesson-box -->';
    }
    ?>

</div>