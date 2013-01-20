<?
$lessonMainData = $lessonData['TeacherLesson'];
if(isSet($lessonData['UserLesson'])) {
    $lessonMainData = $lessonData['UserLesson'];
}
?>

<div class="lesson-box space2 right-student-box2 right-student-newbox2" id="<?php echo $id; ?>">
    <h3 class="radius1">
        <?php echo $this->Layout->lessonTypeIcon($lessonMainData['lesson_type']);
                if(isSet($lessonMainData['datetime'])) {
                    echo $this->TimeTZ->niceShort($lessonMainData['datetime']), ' - ';

                }
                echo '<strong>'.$lessonMainData['name'].'</strong>'; ?>
    </h3>
    <div class="lesson-box-content">
        <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($lessonMainData['image_source'], 72, 72), array('alt' => 'Subject image')); ?></div>
        <div class="usr-text2">

            <div class="form-main-teacher">
                <p class="pull-left"><?php echo $lessonMainData['description']; ?></p>
            </div> <!-- /form-main-teacher -->
        </div> <!-- /usr-text3 -->
    </div> <!-- /lesson-box-content  -->


    <div class="lesson-box-footer radius2">
        <div class="pull-left star"><?php echo $this->Layout->ratingNew($lessonData['Subject']['avarage_rating'], false, 'pull-left pad8'); ?></div>

        <div class="pull-right">
            <?php
            echo $this->Layout->toolTip($this->Layout->buildLessonTooltipHtml(am($lessonData['TeacherLesson'], $lessonMainData)), null, 'pull-right space23', 'tooltip_'.(
                isSet($lessonMainData['user_lesson_id']) ? $lessonMainData['user_lesson_id'] : $lessonMainData['teacher_lesson_id'])
            );
            echo $this->Layout->priceTag($lessonMainData['1_on_1_price'], $lessonMainData['full_group_student_price'], 'price-tag-panel');
            ?>
        </div>
        <?php
        if(!empty($lessonMainData['teacher_lesson_id'])) {
            echo '<span class="pull-left space22 space3">',sprintf(__('Students %d of %d'), $lessonData['TeacherLesson']['num_of_students']
                , $lessonData['TeacherLesson']['max_students']),'</span>';
        } else if($lessonMainData['lesson_type']==LESSON_TYPE_LIVE) {
            echo '<span class="pull-left space22 space3">',sprintf(__('Max students %d '), $lessonMainData['max_students']),'</span>';
        }
        ?>
    </div><!-- /lesson-box-footer -->
</div>