                    <div class="main-student<?php echo (!$first ? ' bot2' : null); ?> teacher-review">
                        <div class="left-student-box">
                            <?php
                            echo $this->Html->image(
                                $this->Layout->image($ratingByTeacher['Teacher']['image_source'], 78, 78),
                                array('alt' => 'Teacher image', 'class'=>'border1'));

                                echo $this->Layout->ratingNew($ratingByStudent['UserLesson']['rating_by_teacher'], false, 'centered');
                            ?>
                        </div>
                        <div class="right-student-box">
                            <div class="pad8">
                                <h6 class="pull-left space10"><strong><?php echo $this->Html->link( $ratingByTeacher['Teacher']['username'],
                                    array('controller'=>'Home', 'action'=>'teacher',
                                        $ratingByTeacher['UserLesson']['teacher_user_id']));
                                    ?></strong></h6>
                                <em class="fontsize3">(Studied at <?php echo date('j,M,Y', strtotime($ratingByTeacher['UserLesson']['datetime'])), null; ?>)</em></div>
                            <p class="studeenmsg"><?php echo $ratingByTeacher['UserLesson']['comment_by_teacher']; ?></p>
                        </div>
                    </div>