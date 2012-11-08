<p class="fontsize1 space8"><?php echo __('Here you can find all invitations that still pending for your approval.'); ?></p>

<?php
echo $this->element('panel/cancel_lesson_popup', array('buttonSelector'=>'.confirm-deny',
                                                             'cancelUrl'=>array('controller'=>'Student', 'action'=>'cancelUserLesson', '{id}')));
echo $this->element('panel/send_msg_popup', array('buttonSelector'=>'.msg-teacher'));
echo $this->element('panel/negotiate_popup', array('buttonSelector'=>'.negotiate'));
?>
<script type="text/javascript">
    $(document).ready(function(){
        //Activate tooltip
        initToolTips();
    });
</script>
<div class="add-sub pull-left space3">

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

        echo '<div class="lesson-box space2" id="user_lesson_id_'.$lessonInvitation['UserLesson']['user_lesson_id'].'">
                <div class="head-back radius1">
                    <h1>'.$this->Time->niceShort($lessonInvitation['UserLesson']['datetime']).' -  <strong>'.$lessonInvitation['UserLesson']['name'].'</strong></h1>
                     <div class="dropdown pull-right">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                            <i class="iconSmall-drop-arrow"></i>
                        </a>
                        <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                            <li><a href="#" class="confirm-accept" data-cancel-prefix="user_lesson_id" data-id="'.$lessonInvitation['UserLesson']['user_lesson_id'].'">'.__('Accept').'</a></li>
                            '.(empty($lessonInvitation['UserLesson']['teacher_lesson_id']) ? '<li><a href="#" class="negotiate" data-remove-element-after-negotiate="#user_lesson_id_'.$lessonInvitation['UserLesson']['user_lesson_id'].'" data-user_lesson_id="'.$lessonInvitation['UserLesson']['user_lesson_id'].'">'.__('Negotiate').'</a></li>' : null).'
                            <li><a href="#" class="confirm-deny" data-cancel-prefix="user_lesson_id" data-id="'.$lessonInvitation['UserLesson']['user_lesson_id'].'">'.__('Deny').'</a></li>
                            <li><a href="#" class="msg-teacher" data-entity_type="lesson" data-entity_id="'.$lessonInvitation['UserLesson']['user_lesson_id'].'" data-to_user_id="'.$lessonInvitation['UserLesson']['teacher_user_id'].'">'.__('Message teacher').'</a></li>
                            <li>'.$toTheLessonLink.'</li>
                        </ul>
                    </div>

                </div>
                <div class="lesson-box-content">
                    <div class="user-pic2">'.$this->Html->image($this->Layout->image($lessonInvitation['UserLesson']['image_source'], 72, 72), array('alt' => 'Lesson image'/*, 'class'=>'border1'*/)).'</div>
                    <div class="usr-text2">
                        <h4>'.__('by').' '.$this->Html->link($lessonInvitation['Teacher']['username'], array('controller'=>'Home', 'action'=>'teacher', $lessonInvitation['UserLesson']['teacher_user_id'])).'</h4>
                        <p>'.$lessonInvitation['UserLesson']['description'].'</p>
                        <p class="space23">'.$toTheLessonLink.'</p>
                    </div>
                </div>
                <div class="lesson-box-footer radius2">
                    <div class="pull-left star">
                        '.$this->Html->image($this->Layout->rating($lessonInvitation['UserLesson']['rating_by_student'], false), array('alt' => 'User rating')).'
                    </div>
                    <div class="pull-right space21 right-i-mar">
                        '.$this->Layout->toolTip($this->Layout->buildLessonTooltipHtml($lessonInvitation['UserLesson']), null, 'pull-right space23', 'tooltip_'.$lessonInvitation['UserLesson']['user_lesson_id']).'
                        '.$this->Layout->priceTag($lessonInvitation['UserLesson']['1_on_1_price'], $lessonInvitation['UserLesson']['full_group_student_price'], 'price-tag-panel').'
                        <!-- <a href="#" class=" pull-right space23"><i class="iconSmall-info r-mor-none"></i></a> -->
                    </div>
                </div> <!-- /lesson-box-footer -->
            </div> <!-- /lesson-box -->';
    }
    ?>

</div>