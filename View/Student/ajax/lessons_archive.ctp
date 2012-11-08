<p class="fontsize1 space8"><?php echo __('Here you can find all your past lessons.'); ?></p>

<div class="add-sub pull-left space3">

    <?php
    foreach($response['response']['archiveLessons'] AS $archiveLessons) {
        /*
        echo 'Message: ',$upcomingLesson['UserLesson']['teacher_user_id'],'-',$upcomingLesson['UserLesson']['student_user_id'],'-',$upcomingLesson['UserLesson']['user_lesson_id'],'<br />';
        echo $this->Html->link('Cancel', array('controller'=>'Student','action'=>'cancelUserLesson', $upcomingLesson['UserLesson']['user_lesson_id']));
        */

        echo '<div class="lesson-box space2">
                <div class="head-back radius1">
                    <h1>'.$this->Time->niceShort($archiveLessons['UserLesson']['datetime']).' -  <strong>'.$archiveLessons['UserLesson']['name'].'</strong></h1>
                </div>
                <div class="lesson-box-content">
                    <div class="user-pic2">'.$this->Html->image($this->Layout->image($archiveLessons['UserLesson']['image_source'], 72, 72), array('alt' => 'Lesson image'/*, 'class'=>'border1'*/)).'</div>
                    <div class="usr-text2">
                        <h4>'.__('by').' '.$this->Html->link($archiveLessons['Teacher']['username'], array('controller'=>'Home', 'action'=>'teacher', $archiveLessons['UserLesson']['teacher_user_id'])).'</h4>
                        <p>'.$archiveLessons['UserLesson']['description'].'</p>
                        <p class="space23">'.$this->Html->link(__('To the lesson'), array('controller'=>'Lessons',
                                                                                            'action'=>($archiveLessons['UserLesson']['lesson_type']==LESSON_TYPE_LIVE ? 'index' : 'video'),
                                                                                            $archiveLessons['UserLesson']['teacher_lesson_id'])).'</p>
                    </div>
                </div>
                <div class="lesson-box-footer radius2">
                    <div class="pull-left star">
                        '.$this->Html->image($this->Layout->rating($archiveLessons['UserLesson']['rating_by_student'], false), array('alt' => 'User rating')).'
                    </div>
                    <div class="pull-right space21 right-i-mar">
                        '.$this->Layout->priceTag($archiveLessons['UserLesson']['1_on_1_price'], $archiveLessons['UserLesson']['full_group_student_price'], 'price-tag-panel').'
                        <a href="#" class=" pull-right"><i class="iconMedium-play  r-mor-none"></i></a>
                        <a href="#" class="pull-right"><i class="iconMedium-store"></i></a>
                        <a href="#" class="pull-right"><i class="iconMedium-mony-fol"></i></a>
                    </div>
                </div> <!-- /lesson-box-footer -->
            </div> <!-- /lesson-box -->';
    }
    ?>
</div>
