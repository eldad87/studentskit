<script type="text/javascript">
    $(document).ready(function(){
        //Activate tooltip
        initToolTips();
    });
</script>

<p class="fontsize1 space8"><?php echo __('Here you can find all invitations requests that still pending for the student\'s approval.'); ?></p>

<?php
echo $this->element('panel/cancel_popup', array('buttonSelector'=>'.confirm-cancel',
    'title'=>__('Cancel an invitation'),
    'description'=>__('This procedure may be irreversible.
                                                                                Do you want to proceed?'),
    'cancelUrl'=>array('controller'=>'Student', 'action'=>'cancelUserLesson', '{id}')));
echo $this->element('panel/send_msg_popup', array('buttonSelector'=>'.msg-student'));
echo $this->element('panel/negotiate_popup', array('buttonSelector'=>'.negotiate'));
?>

    <div class="fullwidth pull-left">
        <ul>
            <li>
                <div class="space2 space6 left-student-box2 left-student-newbox2">
                    <h5 class="pull-left"><strong><?php echo __('Invitation offer\'s data'); ?></strong></h5>
                </div>
                <div class="space2 space6 right-student-box2 right-student-newbox2">
                    <h5 class=" pull-left"><strong><?php echo __('Lesson\'s info'); ?></strong></h5>
                </div>
            </li>

            <?php
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
                                    <li><a href="#" class="msg-student" data-entity_type="lesson" data-entity_id="<?php echo $lessonInvitation['UserLesson']['user_lesson_id']; ?>" data-to_user_id="<?php echo $lessonInvitation['UserLesson']['student_user_id']; ?>"><?php echo __('Message student'); ?></a></li>
                                    <?php
                                        if(empty($lessonInvitation['UserLesson']['teacher_lesson_id'])) {
                                            echo '<li><a href="#" class="negotiate" data-user_lesson_id="'.$lessonInvitation['UserLesson']['user_lesson_id'].'">'.__('Negotiate').'</a></li>';
                                        }
                                    ?>
                                    <li><a href="#" class="confirm-cancel" data-cancel-prefix="user_lesson_id" data-id="<?php echo $lessonInvitation['UserLesson']['user_lesson_id']; ?>"><?php echo __('Cancel'); ?></a></li>
                                    <li><?php echo $toTheLessonLink; ?></li>
                                </ul>
                            </div>

                        </div>

                        <div class="lesson-box-content">
                            <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($lessonInvitation['Student']['image_source'], 72, 72), array('alt' => 'Student image')); ?></div>

                            <div class="usr-text2">
                                <p><?php echo $lessonInvitation['UserLesson']['offer_message']; ?></p>
                            </div>
                        </div>

                        <div class="lesson-box-footer radius2">
                            <div class="pull-left star"><?php echo $this->Html->image($this->Layout->rating($lessonInvitation['Student']['student_avarage_rating'], false), array('alt' => 'Student avarage rating')); ?></div>
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



                    <div class="lesson-box space2 right-student-box2 right-student-newbox2">
                        <h3 class="radius1">
                            <?php echo $this->Time->niceShort($lessonInvitation['UserLesson']['datetime']).' -  <strong>'.$lessonInvitation['UserLesson']['name'].'</strong>'; ?>
                        </h3>
                        <div class="lesson-box-content">
                            <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($lessonInvitation['UserLesson']['image_source'], 72, 72), array('alt' => 'Subject image')); ?></div>
                            <div class="usr-text2">

                                <div class="form-main-teacher">
                                    <p class="pull-left"><?php echo $lessonInvitation['UserLesson']['description']; ?></p>
                                </div> <!-- /form-main-teacher -->
                            </div> <!-- /usr-text3 -->
                        </div> <!-- /lesson-box-content  -->


                        <div class="lesson-box-footer radius2">
                            <div class="pull-left star"><?php echo $this->Html->image($this->Layout->rating($lessonInvitation['Student']['student_avarage_rating'], false), array('alt' => 'Student avarage rating')); ?></div>

                            <div class="pull-right">

                                <?php
                                echo $this->Layout->toolTip($this->Layout->buildLessonTooltipHtml(am($lessonInvitation['TeacherLesson'], $lessonInvitation['UserLesson'])), null, 'pull-right space23', 'tooltip_'.$lessonInvitation['UserLesson']['user_lesson_id']);
                                echo $this->Layout->priceTag($lessonInvitation['UserLesson']['1_on_1_price'], $lessonInvitation['UserLesson']['full_group_student_price'], 'price-tag-panel');
                                ?>
                            </div>
                            <?php
                            if(!empty($lessonInvitation['UserLesson']['teacher_lesson_id'])) {
                                echo '<span class="pull-left space22 space3">',sprintf(__('Students %d of %d'), $lessonInvitation['TeacherLesson']['num_of_students']
                                                                                                                , $lessonInvitation['TeacherLesson']['max_students']),'</span>';
                            } else if($lessonInvitation['UserLesson']['lesson_type']==LESSON_TYPE_LIVE) {
                                echo '<span class="pull-left space22 space3">',sprintf(__('Max students %d '), $lessonInvitation['UserLesson']['max_students']),'</span>';
                            }

                            ?>
                        </div><!-- /lesson-box-footer -->
                    </div>
                </li>
                <?php
            }
            ?>

        </ul>
        <div class="fullwidth pull-left">
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
