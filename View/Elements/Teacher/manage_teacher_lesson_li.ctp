<?php
$toTheLessonLink = $this->Html->link(__('User page'), array('controller'=>'Home', 'action'=>'user', $student['UserLesson']['student_user_id']));
?>
<li id="user_lesson_id_<?php echo $student['UserLesson']['user_lesson_id']; ?>">
    <div class="cont-span16 m-none space8">
        <div class="message-tm-left new-wid">
            <div class="lesson-box">
                <div class="head-back radius1">
                    <h1><?php echo __('Joined on'); ?>: <?php echo $this->TimeTZ->niceShort($student['UserLesson']['datetime']); ?></h1>
                    <div class="dropdown pull-right">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                            <i class="iconSmall-drop-arrow"></i>
                        </a>
                        <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                            <?php
                                if($type=='join') {
                                    //After accept, the box will will be moved to the Student's list
                                    echo '<li><a href="#" class="confirm-accept"
                                            data-move-element-after-accept="#user_lesson_id_'.$student['UserLesson']['user_lesson_id'].'" data-move-to-element-after-accept="#students_list"
                                            data-user_lesson_id="'.$student['UserLesson']['user_lesson_id'].'">'.__('Accept').'</a></li>';
                                }
                            ?>
                            <li><a href="#" class="msg-student" data-entity_type="user_lesson" data-entity_id="<?php echo $student['UserLesson']['user_lesson_id']; ?>" data-to_user_id="<?php echo $student['UserLesson']['student_user_id']; ?>"><?php echo __('Message student'); ?></a></li>
                            <li><?php echo $toTheLessonLink; ?></li>
                            <li><a href="#" class="confirm-delete" data-cancel-prefix="user_lesson_id" data-id="<?php echo $student['UserLesson']['user_lesson_id']; ?>"><?php echo __('Cancel'); ?></a></li>
                        </ul>
                    </div>
                </div>
                <div class="lesson-box-content">
                    <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($student['Student']['image_source'], 72, 72), array('alt' => 'Student image')); ?></div>
                    <div class="usr-text2">
                        <h4><?php echo $student['Student']['username']; ?></h4>
                        <p><?php echo $student['UserLesson']['offer_message']; ?></p>
                        <p class="space23"><?php echo $toTheLessonLink; ?></p>
                    </div>
                </div>
                <div class="lesson-box-footer radius2">
                    <div class="pull-left star">
                        <?php
                        echo $this->Layout->ratingNew($student['Student']['student_average_rating'], false, 'pull-left pad8'); ?>
                    </div>
                </div><!-- /lesson-box-footer -->
            </div> <!-- /lesson-box -->
        </div><!-- /message-tm-left  -->
    </div><!-- /cont-span16  -->
</li>