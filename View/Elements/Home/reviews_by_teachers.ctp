<!-- reviews -->
<div class="lesson-box pad8">
    <h3 class="radius1"><strong><?php echo __('What teachers says about me?'); ?></strong></h3>
    <div class="box-subject2 radius3">


        <?php
        if($ratingByTeachers) {
            $countSRBS = count($ratingByTeachers);
            $i=0;
            foreach($ratingByTeachers AS $ratingByTeacher) { ?>

                <div class="main-student<?php echo (++$i==$countSRBS ? null : ' bod2') ?>">
                    <div class="left-student-box">
                        <?php
                        echo $this->Html->image(
                            $this->Layout->image($ratingByTeacher['Teacher']['image_source'], 78, 78),
                            array('alt' => 'Teacher image', 'class'=>'border1'));

                        echo $this->Html->image($this->Layout->rating($ratingByTeacher['UserLesson']['rating_by_teacher'], false), array('alt' => 'User rating'));
                        ?>
                    </div>
                    <div class="right-student-box">
                        <div class="pad8"><h6 class="pull-left space10"><strong><?php echo $this->Html->link( $ratingByTeacher['Teacher']['username'],
                                                                                                                array('controller'=>'Home', 'action'=>'teacher',
                                                                                                                    $ratingByTeacher['UserLesson']['teacher_user_id']));
                                    ?></strong></h6><em class="fontsize1">(Studied at <?php echo $ratingByTeacher['UserLesson']['datetime']; ?>)</em></div>
                        <p><?php echo $ratingByTeacher['UserLesson']['comment_by_teacher']; ?></p>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>
<!-- /reviews -->