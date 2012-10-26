<!-- Containeer
================================================== -->

<Section class="container">
    <div class="container-inner">
        <div class="row">
            <div class="cont-span12">
                <div class="cont-span16 c-box-mar cbox-space pos">
                    <div class="butn-space">
                        <?php echo $this->fetch('lesson_box'); ?>
                    </div>
                    <div class="student-main-box2 radius3 pad1">
                        <div class="sec-main-box">

                            <?php echo $this->fetch('order_button'); ?>

                            <h6><?php
                                echo $this->Html->link($subjectData['name'], array('controller'=>'Home', 'action'=>'teacherSubject', $subjectData['subject_id']))
                                ?></h6>
                            <p class="pad2"><?php echo $subjectData['description']; ?></p>
                        </div>
                        <div class="icon-box-social  bod2">
                            <div class="social-icons pad2">
                                <a href="#" class="fb"></a>
                                <a href="#" class="twit"></a>
                                <a href="#" class="g-one"></a>
                                <p class="maxstudntbar"><span class="maxstudent">
                                    <?php
                                    if($subjectData['max_students'] && $subjectData['lesson_type']==LESSON_TYPE_LIVE) {
                                        if(!empty($teacherLessonData)) {
                                            echo 'Max. Students: '.$teacherLessonData['num_of_students'].'/'.$subjectData['max_students'];
                                        } else {
                                            echo 'Max. Students: '.$subjectData['max_students'];
                                        }
                                    }
                                    ?>
                                </span><span class="duration">Duration: <?php echo $subjectData['duration_minutes']; ?> min</span></p>
                                <div class="pull-right price-margn"><div class="price-tag"><span><?php echo $this->Layout->priceTag($subjectData['1_on_1_price'], $subjectData['full_group_student_price']); ?></span></div></div>
                            </div>
                        </div>
                        <div class="log-box">
                            <p class="log1 radius3 gradient2"><span class="fontsize4"><?php echo $subjectData['total_lessons'] ?></span><br/>Lessons</p>
                            <p class="log1 radius3 gradient2"><span class="fontsize4"><?php echo $subjectData['students_amount'] ?></span><br/>Students</p>
                            <p class="log1 radius3 gradient2"><span class="fontsize4"><?php echo $subjectData['raters_amount'] ?></span><br/>Reviews</p>

                            <a href="#" class="log2 btn-black radius3"><?php
                            echo $this->Html->image($this->Layout->rating($subjectData['avarage_rating'], false), array('alt' => 'Topic rating'));
                            ?><br/>Rating</a>
                        </div>
                    </div> <!-- /student-main-box -->

                    <?php if($teacherOtherSubjects) { ?>
                    <div class="student-main-box radius3">
                        <h5 class="fullwidth pad8"><strong>My other Subjects</strong></h5>
                        <ul class="subject-box">
                            <?php
                            foreach($teacherOtherSubjects AS $teacherOtherSubject) {
                                echo '<li>',
                                    $this->Html->image($this->Layout->image($teacherOtherSubject['Subject']['image_source'], 128, 95), array('alt' => 'Topic image')),
                                    $this->Html->link('<strong>'.$teacherOtherSubject['Subject']['name'].'</strong>',
                                                array('controller'=>'Home', 'action'=>'teacherSubject', $teacherOtherSubject['Subject']['subject_id']),
                                                array('escape'=>false, 'class'=>'fontsize1')),'

                                    <div class="pull-right"><div class="price-tag"><span>',$this->Layout->priceTag($teacherOtherSubject['Subject']['1_on_1_price'], $teacherOtherSubject['Subject']['full_group_student_price']),'</span></div></div>
                                </li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <?php //if(count($teacherOtherSubjects)>2) {?>
                    <a href="#" class="more radius3 gradient2 space9 pull-left"><strong>Load More</strong><i class="iconSmall-more-arrow"></i></a>
                    <?php
                        //}
                    }

                    if(!empty($upcomingAvailableLessons)) {
                    ?>

                    <div class="student-main-box radius3 space25">
                        <h5 class="fullwidth pad8 pull-left"><strong>Upcoming group lessons</strong></h5>
                        <ul class="subject-morelesson">
                        <?php
                        foreach($upcomingAvailableLessons AS $upcomingAvailableLesson) {
                            echo '<li>
                                <a href="#" class="pull-left">',$this->Html->image($this->Layout->image($upcomingAvailableLesson['TeacherLesson']['image_source'], 58, 58), array('alt' => 'Topic image')),'</a>
                                <div class="upcominglesson-textbox">
                                    <div class="pull-right btn-width">
                                        <div class="price-tag space25 order-price"><span>',$this->Layout->priceTag($upcomingAvailableLesson['TeacherLesson']['1_on_1_price'], $upcomingAvailableLesson['TeacherLesson']['full_group_student_price']),'</span></div>
                                        ',$this->Html->link('Join', array('controller'=>'Order', 'action'=>'init', 'join', $upcomingAvailableLesson['TeacherLesson']['teacher_lesson_id']),
                                                                    array('class'=>'btn-color-gry move-right space35 centered space37')),'
                                    </div>

                                    <div class="space36">
                                        ',$this->Html->link($upcomingAvailableLesson['TeacherLesson']['name'], array('controller'=>'Home', 'action'=>'teacherLesson', $upcomingAvailableLesson['TeacherLesson']['teacher_lesson_id'])),'
                                        <p class="space3">Start :',$upcomingAvailableLesson['TeacherLesson']['datetime'],'</p>
                                        <p>Current student ',$upcomingAvailableLesson['TeacherLesson']['num_of_students'],' of ',$upcomingAvailableLesson['TeacherLesson']['max_students'],'</p>

                                    </div>
                                </div>
                            </li>';

                        }
                        ?>
                        </ul>
                    </div>
                    <a href="#" class="more radius3 gradient2 space9 pull-left"><strong>Load More</strong><i class="iconSmall-more-arrow"></i></a>
                    <?php } ?>
                </div> <!-- /cont-span3 -->
                <div class="cont-span17 cbox-space">
                    <ul class="teacher-box3">
                        <li>
                            <div class="pic-butn-box pos">
                                <div class="student-main-box radius3">
                                    <a title="" href="#" class="teacher-pic radius3"><?php echo $this->Html->image($this->Layout->image($teacherData['image_source'], 149, 182), array('alt' => 'Topic image')); ?></a>
                                    <p class="onliestatus">
                                        <i class="iconMedium-mail pull-left"></i>
                                        <!--<i class="iconSmall-green-dot pull-left space23"></i>
                                        <span class="pull-left online">Online</span>-->
                                    </p>
                                    <div class="head-text3">
                                        <div class="pull-left tutorname-wrapeper">
                                            <?php
                                            echo $this->Html->link('<span class="pad5"><strong>'.$teacherData['username'].'</strong></span>',
                                                                    array('controller'=>'Home', 'action'=>'teacher', $subjectData['user_id']),
                                                                    array('escape'=>false, 'class'=>'tutroaname'));
                                            ?>

                                        </div>
                                        <!--<span class="fontsize1 pad6 pull-left">Expert Math Teacher</span>-->
                                        <p class="pull-left"><?php echo $teacherData['teacher_about']; ?></p>
                                    </div> <!-- /head-text3-->
                                    <div class="butn-box2 pull-left">
                                        <div class="first-box pull-left">
                                            <!--<a href="#" class="btn-color-blue2"><strong>Follow</strong></a>
                                            <a href="#" class="btn-color-gry"><strong>Message</strong></a>-->
                                            &nbsp;
                                        </div>
                                        <div class="second-box pull-left space34">
                                            <h1 class="fontsize1 pad5"><strong>Last 6 Months</strong></h1>
                                            <a href="#" class="black-cent-butn2"><h5><strong>4.56</strong></h5></a>
                                        </div>
                                        <div class="third-box pull-left">
                                            <h1 class="fontsize1 pad5"><strong>All Time</strong></h1>
                                            <a href="#" class="black-cent-butn2"><h5><strong>4.62</strong></h5></a>
                                        </div>
                                    </div>

                                </div> <!-- /student-main-box -->
                            </div> <!-- /pic-butn-box -->
                        </li>
                    </ul>
                    <a href="#" class="more radius3 gradient2 space8"><strong>Load More</strong><i class="iconSmall-more-arrow"></i></a>


                    <div class="lesson-box pad8">
                        <h3 class="radius1"><strong>What student say about me?</strong></h3>
                        <div class="box-subject2 radius3">


                            <?php
                                if($subjectRatingByStudents) {
                                    $countSRBS = count($subjectRatingByStudents);
                                    $i=0;
                                    foreach($subjectRatingByStudents AS $subjectRatingByStudent) { ?>

                            <div class="main-student<?php echo (++$i==$countSRBS ? null : ' bod2') ?>">
                                <div class="left-student-box">
                                    <?php
                                    echo $this->Html->image(
                                        $this->Layout->image($subjectRatingByStudent['Student']['image_source'], 78, 78),
                                        array('alt' => 'User image', 'class'=>'border1'));

                                    echo $this->Html->image($this->Layout->rating($subjectRatingByStudent['UserLesson']['rating_by_student'], false), array('alt' => 'User rating'));
                                    ?>
                                </div>
                                <div class="right-student-box">
                                    <div class="pad8"><h6 class="pull-left space10"><strong><?php echo $subjectRatingByStudent['Student']['username']; ?></strong></h6><em class="fontsize1">(Studied at <?php echo $subjectRatingByStudent['UserLesson']['datetime']; ?>)</em></div>
                                    <p><?php echo $subjectRatingByStudent['UserLesson']['comment_by_student']; ?></p>
                                </div>
                            </div>
                            <?php
                                }
                            }
                            ?>


                        </div>
                    </div>
                    <?php
                    if($subjectRatingByStudents) {
                    ?>
                    <a href="#" class="more radius3 gradient2 space8"><strong>Load More</strong><i class="iconSmall-more-arrow"></i></a>
                    <?php } ?>



                    <div class="lesson-box space8">
                        <h3 class="radius1"><strong>See Other Teachers On This Subject</strong></h3>
                        <div class="box-subject radius2">
                            <?php if(!empty($otherTeacherForThisSubject)) { ?>
                            <a href="#" class="arrow-left arrws2"></a>
                            <ul class="subject-books subject-books1">
                            <?php
                                /*$count = count($otherTeacherForThisSubject);
                                $i=1;*/
                                foreach($otherTeacherForThisSubject AS $otfts) {
                                    //echo '<li',($i++==$count ? 'class="m-none3"' : null),'>';
                                    echo '<li>',$this->Html->image($this->Layout->image($otfts['Teacher']['image_source'], 63, 63), array('alt' => 'Topic image')),'</li>';
                                }

                            ?>
                            </ul>
                            <a href="#" class="arrow-right arrws2"></a>
                            <?php } ?>
                        </div>
                    </div>


                </div> <!-- /cont-span17 -->
            </div> <!-- /cont-span12 -->
        </div><!-- /row -->
    </div><!-- /container-inner -->
</Section>