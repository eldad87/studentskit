<!-- Containeer
================================================== -->
<Section class="container">
    <div class="container-inner">
        <div class="row">
            <div class="cont-span12">
                <div class="cont-span16 c-box-mar cbox-space pos">
                    <div class="student-main-box3 radius3 fix-height">
                        <div class="student-main-inner">
                            <a title="" href="#" class="teacher-pic radius3">
                            <?php
                            echo $this->Html->image($this->Layout->image($teacherData['User']['image_source'], 149, 182), array('alt' => 'Teacher image'))
                            ?>
                            </a>
                            <p class="onliestatus">
                                <i class="iconMedium-mail pull-left"></i>
                                <i class="iconSmall-green-dot pull-left space23"></i>
                                <span class="pull-left online">Online</span>
                            </p>
                            <div class="head-text3">
                                <div class="pull-left tutorname-wrapeper">
                                    <a href="#" class="tutroaname"><span class="pad5"><strong><?php echo $teacherData['User']['username']; ?></strong></span></a>

                                </div>
                                <span class="fontsize1 pad6 pull-left">Since <?php echo $teacherData['User']['created']; ?></span>
                                <p class="pull-left pad8 clear-left"><?php echo $teacherData['User']['teacher_about']; ?></p>
                            </div> <!-- /head-text3-->
                        </div>
                        <div class="icon-box-social  bod2">
                            <div class="social-icons pad2 pad8">
                                <a href="#" class="fb"></a>
                                <a href="#" class="twit"></a>
                                <a href="#" class="g-one"></a>
                            </div>
                        </div>
                        <div class="log-box">
                            <p class="log1 radius3 gradient2"><span class="fontsize4"><?php echo ceil($teacherData['User']['teacher_total_teaching_minutes']/60); ?> H</span><br />Teaching</p>
                            <p class="log1 radius3 gradient2"><span class="fontsize4"><?php echo $teacherData['User']['teacher_total_lessons']; ?></span><br />Lessons</p>
                            <p class="log1 radius3 gradient2"><span class="fontsize4"><?php echo $teacherData['User']['teacher_students_amount']; ?></span><br />Students</p>
                            <a class="log2 btn-black radius3" href="#"><?php
                                echo $this->Html->image($this->Layout->rating($teacherData['User']['teacher_avarage_rating'], false), array('alt' => 'Teacher rating'));
                                ?><br />(<?php echo $teacherData['User']['teacher_avarage_rating']; ?>/<?php echo $teacherData['User']['teacher_raters_amount']; ?> Reviews)</a>
                        </div>
                    </div> <!-- /student-main-box -->

                    <?php
                        echo $this->element('Home/other_subjects', array('teacherSubjects'=>$teacherSubjects));

                        echo $this->element('Home/upcoming_lessons', array('upcomingAvailableLessons'=>$upcomingAvailableLessons));
                    ?>
                </div> <!-- /cont-span3 -->


                <div class="cont-span17 cbox-space ">
                    <?php
                        if(!empty($teacherData['TeacherAboutVideo'][0])) {
                            $this->Html->css('http://vjs.zencdn.net/c/video-js.css', null, array('inline' => false));
                            $this->Html->script('http://vjs.zencdn.net/c/video.js', array('inline' => false));


                    ?>

                    <div class="lesson-box pad8 space4 pull-left">
                        <h3 class="radius1 pull-left"><strong>About video</strong></h3>
                        <div class="box-subject2 radius3 teacher-livevideo pull-left">

                           <?php
                            echo $this->Layout->videoPlayer($teacherData['TeacherAboutVideo'][0]['video_source']);
                            ?>

                        </div>
                    </div>
                    <?php
                        }
                    ?>


                    <?php
                        if(!empty($teacherData['TeacherCertificate'])) {
                    ?>
                    <!-- certification-box -->
                    <div class="lesson-box pad8 space4">
                        <h3 class="radius1"><strong>Certifications</strong></h3>
                        <div class="box-subject2 radius3 fix-height">
                    <?php
                            $tcCount = count($teacherData['TeacherCertificate']);
                            foreach($teacherData['TeacherCertificate'] AS $cert) {
                                echo '<div class="main-student',($tcCount-- ? null : ' bod2'),'">
                                <div class="left-student-box">
                                    ',$this->Html->image($this->Layout->image($cert['image_source'], 78, 78), array('alt' => 'Certificate image', 'class'=>'border1')),'
                                </div>
                                <div class="right-student-box">
                                    <div class="pad8"><h6 class="pull-left space10"><strong>',$cert['name'],'</strong></h6><em class="fontsize1">',($cert['date'] ? $cert['date'] : null),'</em></div>
                                    <p>',$cert['description'],'</p>
                                </div>
                            </div>';
                            }
                    ?>
                        </div>
                    </div><!-- /certification-box -->
                    <?php
                        }
                    ?>
                    <?php
                        if($latestPosts) {
                            echo $this->element('Home/board_topics', array('topics'=>$latestPosts));
                        }

                        echo $this->element('Home/reviews_by_students', array('ratingByStudents'=>$teacherReviews));
                    ?>
                    <!---->

                </div> <!-- /cont-span17 -->
            </div> <!-- /cont-span12 -->
        </div><!-- /row -->
    </div><!-- /container-inner -->
</Section>