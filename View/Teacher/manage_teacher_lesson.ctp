<script type="text/javascript">
    $(document).ready(function(){
        //Activate tooltip
        initToolTips();
    });
</script>

<p class="fontsize1 space8"><?php echo __('Here you can find all booking requests that pending for your approval.'); ?></p>

<?php
echo $this->element('panel/cancel_popup', array('buttonSelector'=>'.confirm-delete',
                                                                    'title'=>__('Cancel'),
                                                                    'description'=>__('This procedure may be irreversible.
                                                                                Do you want to proceed?'),
                                                'cancelUrl'=>array('controller'=>'Student', 'action'=>'cancelUserLesson', '{id}')));
echo $this->element('panel/send_msg_popup', array('buttonSelector'=>'.msg-student'));
echo $this->element('panel/accept_lesson_popup', array('buttonSelector'=>'.confirm-accept'));
?>

<div class="cont-span15 cbox-space">
    <button type="button" class="btn-blue extra-pad space8 load2"  rel="<?php echo Router::url(array('controller'=>'Teacher','action'=>'lessons')); ?>"><i class="iconSmall-add-arrow-left space18"></i>Back to Upcoming</button>

    <div class="clear"></div>

    <div class="fullwidth pull-left">
        <h5 class=" pull-left space2 color-font"><strong><?php echo __('Lesson info'); ?></strong></h5>
    </div>

    <div class="cont-span16 m-none space8">
        <div class="message-tm-right new-wid">
            <div class="lesson-box ">
                <h3 class="radius1"><?php echo $this->Time->niceShort($teacherLesson['datetime']); ?> - <strong><?php echo $teacherLesson['name']; ?></strong></h3>
                <div class="lesson-box-content">
                    <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($teacherLesson['image_source'], 72, 72), array('alt' => 'Lesson image')); ?>
                    </div>
                    <div class="usr-text2">
                        <ul class="lessoninfo-box">
                            <li>
                                <label><?php echo __('Duration minutes'); ?> :</label>
                                <p><?php echo $teacherLesson['duration_minutes']; ?></p>
                            </li>
                            <li>
                                <label><?php echo __('Max students'); ?> :</label>
                                <p><?php echo $teacherLesson['max_students']; ?></p>
                            </li>
                            <li>
                                <label><?php echo __('1 on 1 price'); ?> :</label>
                                <p><?php echo $this->Layout->priceTag($teacherLesson['1_on_1_price'], $teacherLesson['full_group_student_price'], 'space25'); ?></p>
                            </li>
                            <li>
                                <label><?php echo __('Full group student price'); ?> :</label>
                                <p><?php echo $this->Layout->priceTag($teacherLesson['full_group_student_price'], $teacherLesson['full_group_student_price'], 'space25'); ?></p>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="lesson-box-footer radius2">
                    <div class="pull-left star">
                    <?php
                        echo $this->Layout->ratingNew($subjectData['avarage_rating'], false, 'pull-left pad8'); ?>
                    </div>
                </div><!-- /lesson-box-footer -->
            </div><!-- /lesson-box -->
        </div><!-- /message-tm-right -->
    </div><!-- /cont-span16  -->


    <div class="clear"></div>

    <div class="fullwidth pull-left">
        <h5 class=" pull-left space2 color-font"><strong><?php echo __('Current students'); ?></strong></h5>
    </div>
    <ul class="lesson-form" id="students_list">
        <?php
            foreach($allStudents['students'] AS $student) {
                echo $this->element('Teacher/manage_teacher_lesson_li', array('student'=>$student, 'type'=>'student'));
            }
        ?>
    </ul>


    <div class="fullwidth pull-left">
        <h5 class="pull-left space2 color-font"><strong><?php echo __('Join requests'); ?></strong></h5>
    </div>
    <ul class="lesson-form">
        <?php
            foreach($allStudents['join_reuests'] AS $student) {
                echo $this->element('Teacher/manage_teacher_lesson_li', array('student'=>$student, 'type'=>'join'));
            }
        ?>
    </ul>


    <div class="fullwidth pull-left">
        <h5 class=" pull-left space2 color-font"><strong><?php echo __('Invitations sent'); ?></strong></h5>
    </div>
    <ul class="lesson-form">
        <?php
            foreach($allStudents['invitations'] AS $student) {
                echo $this->element('Teacher/manage_teacher_lesson_li', array('student'=>$student, 'type'=>'invitation'));
            }
        ?>
    </ul>
</div>