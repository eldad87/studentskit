<script type="text/javascript">
    $(document).ready(function(){
        //Activate tooltip
        initToolTips();
    });
</script>


<?php
////////////// Page 1 - start
if($page==1) {
?>
    <script type="text/javascript">
        $(document).ready(function(){
            var url = '/Student/lessonsArchive/{limit}/{page}';
            lmObj.loadMoreButton('#user-lessons-archive-load-more', 'click', '#user-lessons-archive', url, {}, 'get', <?php echo $limit; ?>);
            lmObj.setItemsCountSelector('#user-lessons-archive-load-more', '#user-lessons-archive div.lesson-box' );
        });
    </script>

    <p class="fontsize1 space8"><?php echo __('Here you can find all your past lessons.'); ?></p>
    <?php
    echo $this->element('Panel'.DS.'send_msg_popup', array('buttonSelector'=>'.msg-teacher'));
    ?>


    <div class="add-sub pull-left space3" id="user-lessons-archive">

<?php
////////////// Page 1 - ends
}
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
                    <h1>'.$this->Layout->lessonTypeIcon($archiveLessons['UserLesson']['lesson_type']).$this->TimeTZ->niceShort($archiveLessons['UserLesson']['datetime']).' -  <strong>'.$archiveLessons['UserLesson']['name'].'</strong></h1>
                    <div class="dropdown pull-right">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                            <i class="iconSmall-drop-arrow"></i>
                        </a>
                        <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                            <li>'.$this->Html->link(__('Download Receipt'), array('controller'=>'Billing', 'action'=>'downloadReceipt', $archiveLessons['UserLesson']['user_lesson_id'])).'</li>
                            <li><a href="#" class="msg-teacher" data-entity_type="user_lesson" data-entity_id="'.$archiveLessons['UserLesson']['user_lesson_id'].'" data-to_user_id="'.$archiveLessons['UserLesson']['teacher_user_id'].'">'.__('Message teacher').'</a></li>
                            '.($toTheLessonLink ? '<li>'.$toTheLessonLink.'</li>' : null).'
                        </ul>
                    </div>
                </div>
                <div class="lesson-box-content">
                    <div class="user-pic2">'.$this->Html->image($this->Layout->image($archiveLessons['UserLesson']['image_source'], 72, 72), array('alt' => 'Lesson image')).'</div>
                    <div class="usr-text2">
                        <h4>'.__('by').' '.$this->Html->link($archiveLessons['Teacher']['username'], array('controller'=>'Home', 'action'=>'teacher', $archiveLessons['UserLesson']['teacher_user_id'])).'</h4>
                        <p>'.$archiveLessons['UserLesson']['description'].'</p>
                        '.($toTheLessonLink ? '<p class="space23">'.$toTheLessonLink.'</p>' : null).'
                    </div>
                </div>
                <div class="lesson-box-footer radius2">
                    <div class="pull-left star">
                        '.$this->Layout->ratingNew($archiveLessons['UserLesson']['rating_by_student'], false, 'pull-left pad8').'
                    </div>
                    <div class="pull-right space21 right-i-mar">
                        '.$this->Layout->toolTip($this->Layout->buildLessonTooltipHtml(am($archiveLessons['TeacherLesson'], $archiveLessons['UserLesson'])), null, 'pull-right space23', 'tooltip_'.$archiveLessons['UserLesson']['user_lesson_id']).'
                        '.$this->Layout->priceTag($archiveLessons['UserLesson']['price'], $archiveLessons['UserLesson']['bulk_price'], 'price-tag-panel').'
                    </div>
                </div> <!-- /lesson-box-footer -->
            </div> <!-- /lesson-box -->';
    }

////////////// Page 1 - start
if($page==1) {

    ?>
    </div>

    <?php
    if(count($response['response']['archiveLessons'])>=$limit) {
        echo '<a href="#" class="more radius3 gradient2 space8" id="user-lessons-archive-load-more"><strong>', __('Load More') ,'</strong><i class="iconSmall-more-arrow"></i></a>';
    }
}
