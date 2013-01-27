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
        var url = '/Student/lessonsInvitations/{limit}/{page}';
        lmObj.loadMoreButton('#user-lessons-invitations-load-more', 'click', '#user-lessons-invitations', url, {}, 'get', <? echo $limit; ?>);
        lmObj.setItemsCountSelector('#user-lessons-invitations-load-more', '#user-lessons-invitations li' );
    });
</script>

    <p class="fontsize1 space8"><?php echo __('Here you can find all invitations that still pending for your approval.'); ?></p>

    <?php
    echo $this->element('Panel/cancel_popup', array('buttonSelector'=>'.confirm-deny',
        'title'=>__('Cancel an invitation'),
        'description'=>__('This procedure may be irreversible.
                                                                                    Do you want to proceed?'),
        'cancelUrl'=>array('controller'=>'Student', 'action'=>'cancelUserLesson', '{id}')));
    echo $this->element('Panel/send_msg_popup', array('buttonSelector'=>'.msg-teacher'));
    echo $this->element('Panel/negotiate_popup', array('buttonSelector'=>'.negotiate'));
    echo $this->element('Panel/accept_lesson_popup', array('buttonSelector'=>'.confirm-accept'));
    ?>

        <div class="fullwidth pull-left">
            <ul id="user-lessons-invitations">
                <li>
                    <div class="space2 space6 left-student-box2 left-student-newbox2">
                        <h5 class="pull-left"><strong><?php echo __('Invitation\'s request'); ?></strong></h5>
                    </div>
                    <div class="space2 space6 right-student-box2 right-student-newbox2">
                        <h5 class=" pull-left"><strong><?php echo __('Lesson\'s info'); ?></strong></h5>
                    </div>
                </li>

    <?php
}
////////////// Page 1 - end

            foreach($response['response']['lessonInvitations'] AS $lessonInvitation) {
                //Link to the subject page
                $toTheLessonLink = $this->Html->link(__('Lesson page'), array('controller'=>'Home',
                                                                            'action'=> 'teacherSubject',
                                                                            $lessonInvitation['UserLesson']['subject_id']));
                //Link to the teacherLesson page
                if($lessonInvitation['UserLesson']['lesson_type']==LESSON_TYPE_LIVE &&
                    isSet($lessonInvitation['UserLesson']['teacher_lesson_id']) && !empty($lessonInvitation['UserLesson']['teacher_lesson_id'])) {
                    $toTheLessonLink = $this->Html->link(__('Lesson page'), array('controller'=>'Home',
                                                                                    'action'=> 'teacherLesson',
                                                                                    $lessonInvitation['UserLesson']['teacher_lesson_id']));
                }
                ?>

                <li id="user_lesson_id_<?php echo $lessonInvitation['UserLesson']['user_lesson_id']; ?>">
                    <div class="fullwidth pull-left"></div>
                    <div class="lesson-box space2 left-student-box2 left-student-newbox2 pull-left">
                        <div class="head-back radius1">
                            <h1><?php echo $lessonInvitation['Student']['username']; ?></h1>
                            <div class="dropdown pull-right">
                                <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                                    <i class="iconSmall-drop-arrow"></i>
                                </a>
                                <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                                    <li><a href="#" class="confirm-accept" data-remove-element-after-accept="#user_lesson_id_<?php echo $lessonInvitation['UserLesson']['user_lesson_id']; ?>" data-user_lesson_id="<?php echo $lessonInvitation['UserLesson']['user_lesson_id']; ?>"><?php echo __('Accept'); ?></a></li>
                                    <?php
                                        if(empty($lessonInvitation['UserLesson']['teacher_lesson_id'])) {
                                            echo '<li><a href="#" class="negotiate" data-remove-element-after-negotiate="#user_lesson_id_'.$lessonInvitation['UserLesson']['user_lesson_id'].'" data-user_lesson_id="'.$lessonInvitation['UserLesson']['user_lesson_id'].'">'.__('Negotiate').'</a></li>';
                                        }
                                    ?>
                                    <li><a href="#" class="confirm-deny" data-cancel-prefix="user_lesson_id" data-id="<?php echo $lessonInvitation['UserLesson']['user_lesson_id']; ?>"><?php echo __('Deny'); ?></a></li>
                                    <li><a href="#" class="msg-teacher" data-entity_type="user_lesson" data-entity_id="<?php echo $lessonInvitation['UserLesson']['user_lesson_id']; ?>" data-to_user_id="<?php echo $lessonInvitation['UserLesson']['teacher_user_id']; ?>"><?php echo __('Message teacher'); ?></a></li>
                                    <li><?php echo $toTheLessonLink; ?></li>
                                </ul>
                            </div>

                        </div>

                        <div class="lesson-box-content">
                            <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($lessonInvitation['Teacher']['image_source'], 72, 72), array('alt' => 'Teacher image')); ?></div>

                            <div class="usr-text2" id="lesson_box_<?php echo $lessonInvitation['UserLesson']['user_lesson_id']; ?>_msg">
                                <p><?php echo $lessonInvitation['UserLesson']['offer_message']; ?></p>
                            </div>
                        </div>

                        <div class="lesson-box-footer radius2">
                            <div class="pull-left star"><?php echo $this->Layout->ratingNew($lessonInvitation['Teacher']['teacher_avarage_rating'], false, 'pull-left pad8');  ?></div>
                        </div><!-- /lesson-box-footer -->

                    </div><!-- /lesson-box  -->


                    <div class="pull-left refresingbox">
                        <?php
                        if(empty($lessonInvitation['UserLesson']['teacher_lesson_id'])) {
                            echo '<i class="iconBig-new-refresh pull-left"></i>';
                        } else {
                            echo '<i class="iconBig-existing-refresh pull-left"></i>';
                        }
                        ?>
                    </div>


                    <?php
                        echo $this->element('Panel/lesson_box_li', array(
                            'lessonData'        => $lessonInvitation,
                            'id'                => 'lesson_box_'.$lessonInvitation['UserLesson']['user_lesson_id']
                        ));
                    ?>
                </li>
                <?php
            }
            ?>

<?php
////////////// Page 1 - start
if($page==1) {
?>
        </ul>

        <div class="fullwidth pull-left">
            <?php
            if(count($response['response']['lessonInvitations'])>=$limit) {
                echo '<a href="#" class="more radius3 gradient2 space8" id="user-lessons-invitations-load-more"><strong>', __('Load More') ,'</strong><i class="iconSmall-more-arrow"></i></a>';
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
////////////// Page 1 - ends
}
?>