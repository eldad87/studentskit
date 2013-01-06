<div class="lesson-box space2 right-student-box2 right-student-newbox2" id="<?php echo $id; ?>">
    <h3 class="radius1">
        <?php echo $this->Layout->lessonTypeIcon($lessonData['UserLesson']['lesson_type']).
        $this->Time->niceShort($lessonData['UserLesson']['datetime']).' -  <strong>'.$lessonData['UserLesson']['name'].'</strong>'; ?>
    </h3>
    <div class="lesson-box-content">
        <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($lessonData['UserLesson']['image_source'], 72, 72), array('alt' => 'Subject image')); ?></div>
        <div class="usr-text2">

            <div class="form-main-teacher">
                <p class="pull-left"><?php echo $lessonData['UserLesson']['description']; ?></p>
            </div> <!-- /form-main-teacher -->
        </div> <!-- /usr-text3 -->
    </div> <!-- /lesson-box-content  -->


    <div class="lesson-box-footer radius2">
        <div class="pull-left star"><?php echo $this->Layout->ratingNew($lessonData['Subject']['avarage_rating'], false, 'pull-left pad8'); ?></div>

        <div class="pull-right">
            <?php
            echo $this->Layout->toolTip($this->Layout->buildLessonTooltipHtml(am($lessonData['TeacherLesson'], $lessonData['UserLesson'])), null, 'pull-right space23', 'tooltip_'.$lessonData['UserLesson']['user_lesson_id']);
            echo $this->Layout->priceTag($lessonData['UserLesson']['1_on_1_price'], $lessonData['UserLesson']['full_group_student_price'], 'price-tag-panel');
            ?>
        </div>
        <?php
        if(!empty($lessonData['UserLesson']['teacher_lesson_id'])) {
            echo '<span class="pull-left space22 space3">',sprintf(__('Students %d of %d'), $lessonData['TeacherLesson']['num_of_students']
                , $lessonData['TeacherLesson']['max_students']),'</span>';
        } else if($lessonData['UserLesson']['lesson_type']==LESSON_TYPE_LIVE) {
            echo '<span class="pull-left space22 space3">',sprintf(__('Max students %d '), $lessonData['UserLesson']['max_students']),'</span>';
        }
        ?>
    </div><!-- /lesson-box-footer -->
</div>