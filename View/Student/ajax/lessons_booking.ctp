<p class="fontsize1 space8"><?php echo __('Here you can find all lesson requests that still pending for the teacher\'s approval.'); ?></p>

<div class="add-sub pull-left space3">

    <?php
    foreach($response['response']['bookingLessons'] AS $bookingLesson) {

        echo '<div class="lesson-box space2">
                <div class="head-back radius1">
                    <h1>'.$this->Time->niceShort($bookingLesson['UserLesson']['datetime']).' -  <strong>'.$bookingLesson['UserLesson']['name'].'</strong></h1>
                    <a href="#" class=" pull-right space23 show-tip" id="drop8"><i class="iconSmall-drop-arrow"></i></a>
                    <a href="#" class="pull-right space27"><i class="iconMedium-mail  bot-pad"></i></a>
                    <a href="#" class="pull-right space23"><i class="iconSmall-info"></i></a>
                </div>
                <div class="lesson-box-content">
                    <div class="user-pic2">'.$this->Html->image($this->Layout->image($bookingLesson['UserLesson']['image_source'], 72, 72), array('alt' => 'Lesson image'/*, 'class'=>'border1'*/)).'</div>
                    <div class="usr-text2">
                        <h4>'.__('by').' '.$this->Html->link($bookingLesson['Teacher']['username'], array('controller'=>'Home', 'action'=>'teacher', $bookingLesson['UserLesson']['teacher_user_id'])).'</h4>
                        <p>'.$bookingLesson['UserLesson']['description'].'</p>
                        <p class="space23">'.$this->Html->link(__('To the lesson'), array('controller'=>'Lessons',
                                                                                            'action'=>($bookingLesson['UserLesson']['lesson_type']==LESSON_TYPE_LIVE ? 'index' : 'video'),
                                                                                            $bookingLesson['UserLesson']['teacher_lesson_id'])).'</p>
                    </div>
                </div>
                <div class="lesson-box-footer radius2">
                    <div class="pull-left star">
                        '.$this->Html->image($this->Layout->rating($bookingLesson['UserLesson']['rating_by_student'], false), array('alt' => 'User rating')).'
                    </div>
                    <div class="pull-right space21 right-i-mar">
                        '.$this->Layout->priceTag($bookingLesson['UserLesson']['1_on_1_price'], $bookingLesson['UserLesson']['full_group_student_price'], 'price-tag-panel').'
                        <!-- <a href="#" class=" pull-right space23"><i class="iconSmall-info r-mor-none"></i></a> -->
                    </div>
                </div> <!-- /lesson-box-footer -->
            </div> <!-- /lesson-box -->';
    }
    ?>

</div>