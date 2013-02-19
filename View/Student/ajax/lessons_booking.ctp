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
            var url = '/Student/lessonsBooking/{limit}/{page}';
            lmObj.loadMoreButton('#user-lessons-booking-load-more', 'click', '#user-lessons-booking', url, {}, 'get', <? echo $limit; ?>);
            lmObj.setItemsCountSelector('#user-lessons-booking-load-more', '#user-lessons-booking li' );
        });
    </script>


    <p class="fontsize1 space8"><?php echo __('Here you can find all lesson requests that still pending for the teacher\'s approval.'); ?></p>


<?php
echo $this->element('Panel'.DS.'cancel_popup', array('buttonSelector'=>'.confirm-cancel',
                                                'title'=>__('Cancel your booking request'),
                                                'description'=>__('This procedure may be irreversible.
                                                                    Do you want to proceed?'),
                                                'cancelUrl'=>array('controller'=>'Student', 'action'=>'cancelUserLesson', '{id}')));
echo $this->element('Panel'.DS.'send_msg_popup', array('buttonSelector'=>'.msg-teacher'));
echo $this->element('Panel'.DS.'negotiate_popup', array('buttonSelector'=>'.negotiate'));
?>

    <div class="fullwidth pull-left">
        <ul id="user-lessons-booking">
            <li>
                <div class="space2 space6 left-student-box2 left-student-newbox2">
                    <h5 class="pull-left"><strong><?php echo __('Booking request\'s data'); ?></strong></h5>
                </div>
                <div class="space2 space6 right-student-box2 right-student-newbox2">
                    <h5 class=" pull-left"><strong><?php echo __('Lesson\'s info'); ?></strong></h5>
                </div>
            </li>

            <?php
}
////////////// Page 1 - ends

            foreach($response['response']['bookingLessons'] AS $lessonBooking) {
                //Link to the subject page
                $toTheLessonLink = $this->Html->link(__('Lesson page'), array('controller'=>'Home',
                                                                                'action'=> 'teacherSubject',
                                                                                $lessonBooking['UserLesson']['subject_id']));

                //Link to the teacherLesson page
                if($lessonBooking['UserLesson']['lesson_type']==LESSON_TYPE_LIVE &&
                    isSet($lessonBooking['UserLesson']['teacher_lesson_id']) && !empty($lessonBooking['UserLesson']['teacher_lesson_id'])) {

                    $toTheLessonLink = $this->Html->link(__('Lesson page'), array('controller'=>'Home',
                                                                                    'action'=> 'teacherLesson',
                                                                                    $lessonBooking['UserLesson']['teacher_lesson_id']));
                }
                ?>

                <li id="user_lesson_id_<?php echo $lessonBooking['UserLesson']['user_lesson_id']; ?>">
                    <div class="fullwidth pull-left"></div>
                    <div class="lesson-box space2 left-student-box2 left-student-newbox2 pull-left">
                        <div class="head-back radius1">
                            <h1><?php echo $lessonBooking['Student']['username']; ?></h1>
                            <div class="dropdown pull-right">
                                <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                                    <i class="iconSmall-drop-arrow"></i>
                                </a>
                                <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                                    <li><a href="#" class="msg-teacher" data-entity_type="user_lesson" data-entity_id="<?php echo $lessonBooking['UserLesson']['user_lesson_id']; ?>" data-to_user_id="<?php echo $lessonBooking['UserLesson']['teacher_user_id']; ?>"><?php echo __('Message teacher'); ?></a></li>
                                    <?php
                                        if(empty($lessonBooking['UserLesson']['teacher_lesson_id'])) {
                                            echo '<li><a href="#" class="negotiate" data-update-lesson-box-after-negotiate="lesson_box_'.$lessonBooking['UserLesson']['user_lesson_id'].'" data-user_lesson_id="'.$lessonBooking['UserLesson']['user_lesson_id'].'">'.__('Negotiate').'</a></li>';
                                        }
                                    ?>
                                    <li><a href="#" class="confirm-cancel" data-cancel-prefix="user_lesson_id" data-id="<?php echo $lessonBooking['UserLesson']['user_lesson_id']; ?>"><?php echo __('Cancel'); ?></a></li>
                                    <li><?php echo $toTheLessonLink; ?></li>
                                </ul>
                            </div>

                        </div>

                        <div class="lesson-box-content">
                            <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($lessonBooking['Teacher']['image_source'], 72, 72), array('alt' => 'Teacher image')); ?></div>

                            <div class="usr-text2" id="lesson_box_<?php echo $lessonBooking['UserLesson']['user_lesson_id']; ?>_msg">
                                <p><?php echo $lessonBooking['UserLesson']['offer_message']; ?></p>
                            </div>
                        </div>

                        <div class="lesson-box-footer radius2">
                            <div class="pull-left star"><?php echo $this->Layout->ratingNew($lessonBooking['Teacher']['teacher_avarage_rating'], false, 'pull-left pad8'); ?></div>
                        </div><!-- /lesson-box-footer -->

                    </div><!-- /lesson-box  -->


                    <div class="pull-left refresingbox">
                        <?php
                        if(empty($lessonBooking['UserLesson']['teacher_lesson_id'])) {
                            echo '<i class="iconBig-new-refresh pull-left"></i>';
                        } else {
                            echo '<i class="iconBig-existing-refresh pull-left"></i>';
                        }
                        ?>
                    </div>



                    <?php
                    echo $this->element('Panel'.DS.'lesson_box_li', array(
                        'lessonData'        => $lessonBooking,
                        'id'                => 'lesson_box_'.$lessonBooking['UserLesson']['user_lesson_id']
                    ));
                    ?>
                    </div>
                </li>
                <?php
            }

////////////// Page 1 - start
if($page==1) {
        ?>

        </ul>
        <div class="fullwidth pull-left">
            <?php
            if(count($response['response']['bookingLessons'])>=$limit) {
                echo '<a href="#" class="more radius3 gradient2 space8" id="user-lessons-booking-load-more"><strong>', __('Load More') ,'</strong><i class="iconSmall-more-arrow"></i></a>';
            }
            ?>
            <div class="fullwidth pull-left">
                <i class="iconBig-new-refresh pull-left"></i>
                <p class="space26 space24"><?php echo __('A request for a new lesson'); ?></p>
            </div>
            <div class="fullwidth pull-left space9">
                <i class="iconBig-existing-refresh pull-left"></i>
                <p class="space26 space24"><?php echo __('A request to join an existing lesson'); ?></p>
            </div>
        </div>
    </div><!-- /left-student-box-->
<?php
////////////// Page 1 - start
}