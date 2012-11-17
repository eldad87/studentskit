<script type="text/javascript">
    $(document).ready(function(){
        //Activate tooltip
        initToolTips();
    });
</script>

<p class="fontsize1 space8"><?php echo __('Here you can find all booking requests that pending for your approval.'); ?></p>

<?php
echo $this->element('panel/cancel_popup', array('buttonSelector'=>'.confirm-deny',
    'title'=>__('Cancel a booking request'),
    'description'=>__('This procedure may be irreversible.
                                                                                Do you want to proceed?'),
    'cancelUrl'=>array('controller'=>'Student', 'action'=>'cancelUserLesson', '{id}')));
echo $this->element('panel/send_msg_popup', array('buttonSelector'=>'.msg-student'));
echo $this->element('panel/negotiate_popup', array('buttonSelector'=>'.negotiate'));
echo $this->element('panel/accept_lesson_popup', array('buttonSelector'=>'.confirm-accept'));
?>


<div class="fullwidth pull-left">
    <p>Booking requests from users.<a href="#"> click here</a> to enable the auto-approve mechanism.</p>

    <div class="fullwidth pull-left">
        <ul>
            <li>
                <div class="space2 space6 left-student-box2 left-student-newbox2">
                    <h5 class="pull-left"><strong><?php echo __('Student\'s request'); ?></strong></h5>
                    </div>
                <div class="space2 space6 right-student-box2 right-student-newbox2">
                    <h5 class=" pull-left"><strong><?php echo __('Lesson\'s info'); ?></strong></h5>
                </div>
            </li>

<?php
    foreach($response['response']['bookingRequests'] AS $bookingRequest) {
        //Link to the subject page
        $toTheLessonLink = $this->Html->link(__('Lesson page'), array('controller'=>'Home',
            'action'=> 'teacherSubject',
            $bookingRequest['UserLesson']['subject_id']));

        //Link to the teacherLesson page
        if($bookingRequest['UserLesson']['lesson_type']==LESSON_TYPE_LIVE &&
            isSet($bookingRequest['UserLesson']['teacher_lesson_id']) && !empty($bookingRequest['UserLesson']['teacher_lesson_id'])) {
            $toTheLessonLink = $this->Html->link(__('Lesson page'), array('controller'=>'Home',
                                                                            'action'=> 'teacherLesson',
                                                                            $bookingRequest['UserLesson']['teacher_lesson_id']));
        }
?>

            <li id="user_lesson_id_<?php echo $bookingRequest['UserLesson']['user_lesson_id']; ?>">
                <div class="fullwidth pull-left"></div>
                <div class="lesson-box space2 left-student-box2 left-student-newbox2 pull-left">
                    <div class="head-back radius1">
                        <h1><?php echo $bookingRequest['Student']['username']; ?></h1>
                        <div class="dropdown pull-right">
                            <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                                <i class="iconSmall-drop-arrow"></i>
                            </a>
                            <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                                <li><a href="#" class="confirm-accept" data-remove-element-after-accept="#user_lesson_id_<?php echo $bookingRequest['UserLesson']['user_lesson_id']; ?>" data-user_lesson_id="<?php echo $bookingRequest['UserLesson']['user_lesson_id']; ?>"><?php echo __('Accept'); ?></a></li>
                                <?php
                                    if(empty($bookingRequest['UserLesson']['teacher_lesson_id'])) {
                                        echo '<li><a href="#" class="negotiate" data-remove-element-after-negotiate="#user_lesson_id_'.$bookingRequest['UserLesson']['user_lesson_id'].'" data-update-tooltip-after-negotiate="#tooltip_'.$bookingRequest['UserLesson']['user_lesson_id'].'" data-user_lesson_id="'.$bookingRequest['UserLesson']['user_lesson_id'].'">'.__('Negotiate').'</a></li>';
                                    }
                                ?>
                                <li><a href="#" class="confirm-deny" data-cancel-prefix="user_lesson_id" data-id="<?php echo $bookingRequest['UserLesson']['user_lesson_id']; ?>"><?php echo __('Deny'); ?></a></li>
                                <li><a href="#" class="msg-student" data-entity_type="lesson" data-entity_id="<?php echo $bookingRequest['UserLesson']['user_lesson_id']; ?>" data-to_user_id="<?php echo $bookingRequest['UserLesson']['student_user_id']; ?>"><?php echo __('Message student'); ?></a></li>
                                <li><?php echo $toTheLessonLink; ?></li>
                            </ul>
                        </div>

                    </div>

                    <div class="lesson-box-content">
                        <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($bookingRequest['Student']['image_source'], 72, 72), array('alt' => 'Student image')); ?></div>

                        <div class="usr-text2">
                            <p><?php echo $bookingRequest['UserLesson']['offer_message']; ?></p>
                        </div>
                    </div>

                    <div class="lesson-box-footer radius2">
                        <div class="pull-left star"><?php echo $this->Layout->ratingNew($bookingRequest['Student']['student_avarage_rating'], false, 'pull-left pad8'); ?></div>
                    </div><!-- /lesson-box-footer -->

                </div><!-- /lesson-box  -->


                <div class="pull-left refresingbox">
                <?php
                    if(empty($bookingRequest['UserLesson']['teacher_lesson_id'])) {
                        echo '<i class="iconBig-new-refresh pull-left"></i>';
                    } else {
                        echo '<i class="iconBig-existing-refresh pull-left"></i>';
                    }
                ?>
                </div>



                <div class="lesson-box space2 right-student-box2 right-student-newbox2">
                    <h3 class="radius1">
                        <?php echo $this->Layout->lessonTypeIcon($bookingRequest['UserLesson']['lesson_type']).
                                    $this->Time->niceShort($bookingRequest['UserLesson']['datetime']).' -  <strong>'.$bookingRequest['UserLesson']['name'].'</strong>'; ?>
                    </h3>
                    <div class="lesson-box-content">
                        <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($bookingRequest['UserLesson']['image_source'], 72, 72), array('alt' => 'Subject image')); ?></div>
                        <div class="usr-text2">

                            <div class="form-main-teacher">
                                <p class="pull-left"><?php echo $bookingRequest['UserLesson']['description']; ?></p>
                            </div> <!-- /form-main-teacher -->
                        </div> <!-- /usr-text3 -->
                    </div> <!-- /lesson-box-content  -->


                    <div class="lesson-box-footer radius2">
                        <div class="pull-left star"><?php echo $this->Layout->ratingNew($bookingRequest['Student']['student_avarage_rating'], false, 'pull-left pad8'); ?></div>

                        <div class="pull-right">
                            <?php
                                echo $this->Layout->toolTip($this->Layout->buildLessonTooltipHtml(am($bookingRequest['TeacherLesson'], $bookingRequest['UserLesson'])), null, 'pull-right space23', 'tooltip_'.$bookingRequest['UserLesson']['user_lesson_id']);
                                echo $this->Layout->priceTag($bookingRequest['UserLesson']['1_on_1_price'], $bookingRequest['UserLesson']['full_group_student_price'], 'price-tag-panel');
                            ?>
                        </div>
                        <?php
                            if(!empty($bookingRequest['UserLesson']['teacher_lesson_id'])) {
                                echo '<span class="pull-left space22 space3">',sprintf(__('Students %d of %d'), $bookingRequest['TeacherLesson']['num_of_students']
                                                                                                                , $bookingRequest['TeacherLesson']['max_students']),'</span>';
                            } else if($bookingRequest['UserLesson']['lesson_type']==LESSON_TYPE_LIVE) {
                                echo '<span class="pull-left space22 space3">',sprintf(__('Max students %d '), $bookingRequest['UserLesson']['max_students']),'</span>';
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
</div>


    <?php die;?>
<p class="fontsize1 space8"><?php echo __('Here you can find all invitations requests that still pending for your approval.'); ?></p>

<?php
echo $this->element('panel/cancel_popup', array('buttonSelector'=>'.confirm-deny',
    'title'=>__('Cancel a booking request'),
    'description'=>__('This procedure may be irreversible.
                                                                                Do you want to proceed?'),
    'cancelUrl'=>array('controller'=>'Student', 'action'=>'cancelUserLesson', '{id}')));
echo $this->element('panel/send_msg_popup', array('buttonSelector'=>'.msg-student'));
echo $this->element('panel/negotiate_popup', array('buttonSelector'=>'.negotiate'));
echo $this->element('panel/accept_lesson_popup', array('buttonSelector'=>'.confirm-accept'));
?>
<script type="text/javascript">
    $(document).ready(function(){
        //Activate tooltip
        initToolTips();
    });
</script>
<div class="add-sub pull-left space3">

    <?php
    foreach($response['response']['bookingRequests'] AS $bookingRequest) {

        //Link to the subject page
        $toTheLessonLink = $this->Html->link(__('Lesson page'), array('controller'=>'Home',
                                                                    'action'=> 'teacherSubject',
                                                                    $bookingRequest['UserLesson']['subject_id']));
        //Link to the teacherLesson page
        if($bookingRequest['UserLesson']['lesson_type']==LESSON_TYPE_LIVE &&
            isSet($bookingRequest['UserLesson']['teacher_lesson_id']) && !empty($bookingRequest['UserLesson']['teacher_lesson_id'])) {
            $toTheLessonLink = $this->Html->link(__('Lesson page'), array('controller'=>'Home',
                                                                            'action'=> 'teacherLesson',
                                                                            $bookingRequest['UserLesson']['teacher_lesson_id']));
        }

        echo '<div class="lesson-box space2" id="user_lesson_id_'.$bookingRequest['UserLesson']['user_lesson_id'].'">
                <div class="head-back radius1">
                    <h1>'.$this->Time->niceShort($bookingRequest['UserLesson']['datetime']).' -  <strong>'.$bookingRequest['UserLesson']['name'].'</strong></h1>
                     <div class="dropdown pull-right">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                            <i class="iconSmall-drop-arrow"></i>
                        </a>
                        <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                            <li><a href="#" class="confirm-accept" data-remove-element-after-accept="#user_lesson_id_'.$bookingRequest['UserLesson']['user_lesson_id'].'" data-user_lesson_id="'.$bookingRequest['UserLesson']['user_lesson_id'].'">'.__('Accept').'</a></li>
                            '.(empty($bookingRequest['UserLesson']['teacher_lesson_id']) ? '<li><a href="#" class="negotiate" data-remove-element-after-negotiate="#user_lesson_id_'.$bookingRequest['UserLesson']['user_lesson_id'].'" data-update-tooltip-after-negotiate="#tooltip_'.$bookingRequest['UserLesson']['user_lesson_id'].'" data-user_lesson_id="'.$bookingRequest['UserLesson']['user_lesson_id'].'">'.__('Negotiate').'</a></li>' : null).'
                            <li><a href="#" class="confirm-deny" data-cancel-prefix="user_lesson_id" data-id="'.$bookingRequest['UserLesson']['user_lesson_id'].'">'.__('Deny').'</a></li>
                            <li><a href="#" class="msg-student" data-entity_type="lesson" data-entity_id="'.$bookingRequest['UserLesson']['user_lesson_id'].'" data-to_user_id="'.$bookingRequest['UserLesson']['student_user_id'].'">'.__('Message student').'</a></li>
                            <li>'.$toTheLessonLink.'</li>
                        </ul>
                    </div>

                </div>
                <div class="lesson-box-content">
                    <div class="user-pic2">'.$this->Html->image($this->Layout->image($bookingRequest['UserLesson']['image_source'], 72, 72), array('alt' => 'Lesson image'/*, 'class'=>'border1'*/)).'</div>
                    <div class="usr-text2">
                        <p>'.$bookingRequest['UserLesson']['description'].'</p>
                        <p class="space23">'.$toTheLessonLink.'</p>
                    </div>
                </div>
                <div class="lesson-box-footer radius2">
                    <div class="pull-left star">
                        '.$this->Layout->ratingNew($bookingRequest['UserLesson']['rating_by_student'], false, 'pull-left pad8').'
                    </div>
                    <div class="pull-right space21 right-i-mar">
                        '.$this->Layout->toolTip($this->Layout->buildLessonTooltipHtml($bookingRequest['UserLesson']), null, 'pull-right space23', 'tooltip_'.$bookingRequest['UserLesson']['user_lesson_id']).'
                        '.$this->Layout->priceTag($bookingRequest['UserLesson']['1_on_1_price'], $bookingRequest['UserLesson']['full_group_student_price'], 'price-tag-panel').'
                        <!-- <a href="#" class=" pull-right space23"><i class="iconSmall-info r-mor-none"></i></a> -->
                    </div>
                </div> <!-- /lesson-box-footer -->
            </div> <!-- /lesson-box -->';
    }
    ?>

</div>