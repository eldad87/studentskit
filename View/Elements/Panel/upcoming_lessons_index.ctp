<div class="message-tm-main">
    <h5 class="space2"><strong><?php echo __('Lesson About To Start'); ?></strong></h5>

    <?php
    $i = 0;
    foreach($upcomingLessons AS $upcomingLesson) {
        $i++;
        $lessonData = isSet($upcomingLesson['UserLesson']) ? $upcomingLesson['UserLesson'] : $upcomingLesson['TeacherLesson'];

        $lessonLinkText = sprintf(__('Starts on: %s'), $this->TimeTZ->niceShort($lessonData['datetime']));
        $toTheLessonLink = $this->Html->link($lessonLinkText, array('controller'=>'Lessons',
                                                                                    'action'=>($lessonData['lesson_type']==LESSON_TYPE_LIVE ? 'index' : 'video'),
                                                                                    $lessonData['teacher_lesson_id']),
                                                                array('escape'=>false)
        );
    ?>
    <div class="<?php echo ($i%2==0 ? 'message-tm-right' : 'message-tm-left'); ?> space8">
        <div class="lesson-box">

            <div class="radius1">
                <h3><?php echo $this->Layout->lessonTypeIcon($lessonData['lesson_type']); ?> <strong><?php echo $lessonData['name']; ?></strong></h3>
            </div>

            <div class="lesson-box-content">
                <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($lessonData['image_source'], 72, 72),
                                                                        array('alt'=>'Lesson Image')); ?></div>
                <div class="usr-text2">
                    <?php
                    if(isSet($upcomingLesson['Teacher']['username'])) {
                        echo '<h4>',__('by'),' ',$upcomingLesson['Teacher']['username'],'</h4>';
                    }
                    ?>

                    <p><?php echo $lessonData['description']; ?></p>
                    <?php echo $toTheLessonLink; ?>
                </div>
            </div>
            <div class="lesson-box-footer radius2">

                <div class="pull-left star"><?php echo $this->Layout->ratingNew($upcomingLesson['Subject']['avarage_rating'], false, 'pull-left pad8'); ?></div>
                <div class="pull-right">
                    <?php echo $this->Layout->priceTag($lessonData['full_group_total_price'], $lessonData['full_group_student_price']); ?>
                    <!--<a href="#"><i class="iconSmall-info space3"></i></a>-->
                </div>
            </div><!-- /lesson-box-footer -->
        </div> <!-- /lesson-box -->
    </div><!-- /message-tm-left -->
    <?php }
    if(!$upcomingLessons) {
        echo '<p>',__('No upcoming lessons'),'</p>';
    }
    ?>
</div><!-- /message-tm-main -->