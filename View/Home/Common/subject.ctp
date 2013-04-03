<?php
echo $this->element('Panel'.DS.'send_msg_popup', array('buttonSelector'=>'.msg-teacher'));
echo $this->fetch('popups');


if(isSet($teacherLessonData)) {
$this->Html->scriptBlock('
    $(document).ready(function() {
        mixpanel.track("Home. teacher lesson load);

        $(\'.msg-teacher\').click(function() {
            mixpanel.track("Home. Teacher Lesson pm click");
        });

        $(\'.other-subject\').click(function() {
            mixpanel.track("Home. Teacher Lesson other subject click");
        });

         $(\'.upcoming-lesson-join\').click(function() {
            mixpanel.track("Home. Teacher Lesson upcoming lesson join click");
        });
         $(\'.upcoming-lesson-open\').click(function() {
            mixpanel.track("Home. Teacher Lesson upcoming lesson open click");
        });
        $(\'.order-button\').click(function() {
            mixpanel.track("Home. Teacher Lesson order click");
        });

    });
    ', array('inline'=>false));

} else {

$this->Html->scriptBlock('
    $(document).ready(function() {
        mixpanel.track("Home. teacher subject load);

        $(\'.msg-teacher\').click(function() {
            mixpanel.track("Home. Teacher Subject pm click");
        });

        $(\'.other-subject\').click(function() {
            mixpanel.track("Home. Teacher Subject other subject click");
        });

         $(\'.upcoming-lesson-join\').click(function() {
            mixpanel.track("Home. Teacher Subject upcoming lesson join click");
        });
         $(\'.upcoming-lesson-open\').click(function() {
            mixpanel.track("Home. Teacher Subject upcoming lesson open click");
        });
        $(\'.order-button\').click(function() {
            mixpanel.track("Home. Teacher Subject order click");
        });
    });
    ', array('inline'=>false));
}
?>
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

                            <?php echo $this->fetch('topic_image'); ?>

                            <h6 class="space4 pad8">
                                <i class="iconSmall-flag pointer contact-request"
                                            data-subject="<?php
                                                                if(isSet($teacherLessonData)) {
                                                                    echo __('Report on teacher lesson '), $teacherLessonData['teacher_lesson_id'];
                                                                } else {
                                                                    echo __('Report on teacher subject '), $subjectData['subject_id'];
                                                                }
                                            ?>"
                                            data-topic="<?php App::import('Model', 'Contact'); echo Contact::CONTACT_FLAG; ?>"></i>
                                <?php
                                echo $subjectData['name'];
                                //echo $this->Html->link($subjectData['name'], array('controller'=>'Home', 'action'=>'teacherSubject', $subjectData['subject_id']), array('class'=>'clear'))
                                ?></h6>
                            <p class="pad2"><?php echo $subjectData['description']; ?></p>
                        </div>
                        <div class="icon-box-social bod2">
                            <div class="social-icons pad2">
                                <div class="pull-left">
                                    <?php echo $this->Facebook->like(array('show_faces'=>'false', 'layout'=>'button_count')); ?>
                                </div>
                                <!--<a href="#" class="fb"></a>
                                <a href="#" class="twit"></a>
                                <a href="#" class="g-one"></a>-->
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
                                </span>
                                    <span class="duration">
                                    <?php
                                    echo sprintf(__('Duration %s min'), $subjectData['duration_minutes']);
                                    ?></span></p>
                                <div class="pull-right price-margn">
                                    <?php echo $this->Layout->priceTag($subjectData['1_on_1_price'], $subjectData['full_group_student_price']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="log-box">
                            <p class="log1 radius3 gradient2"><span class="fontsize4"><?php echo $subjectData['total_lessons'] ?></span><br/><?php echo __('Lessons'); ?></p>
                            <p class="log1 radius3 gradient2"><span class="fontsize4"><?php echo $subjectData['students_amount'] ?></span><br/><?php echo __('Students'); ?></p>
                            <p class="log1 radius3 gradient2"><span class="fontsize4"><?php echo $subjectData['raters_amount'] ?></span><br/><?php echo __('Reviews'); ?></p>

                            <a href="#" class="log2 btn-black radius3"><?php
                                echo $this->Layout->ratingNew($subjectData['avarage_rating'], false, 'space20 centered');
                            ?><br/>Rating</a>
                        </div>
                    </div> <!-- /student-main-box -->

                    <?php
                    //if($teacherOtherSubjects) {
                        echo $this->element('Home'.DS.'other_subjects', array('teacherSubjects'=>$teacherOtherSubjects));
                    //  }

                    if(isSet($upcomingAvailableLessons)) {
                        echo $this->element('Home'.DS.'upcoming_lessons', array('upcomingAvailableLessons'=>$upcomingAvailableLessons));
                    }
                    ?>
                </div>
                <div class="cont-span17 cbox-space">
                    <ul class="teacher-box3">
                        <li>
                            <div class="pic-butn-box pos fix-width">
                                <div class="student-main-box radius3 fix-height">
                                    <a title="" href="#" class="teacher-pic radius3"><?php echo $this->Html->image($this->Layout->image($teacherData['image_source'], 149, 182), array('alt' => 'Topic image')); ?></a>
                                    <p class="onliestatus">
                                        <?php

                                        ?>
                                        <a href="#" class="msg-teacher requireLogin" data-to_user_id="<?php echo $teacherData['user_id']; ?>"<?php
                                            if($subjectData['lesson_type']==LESSON_TYPE_LIVE && !empty($teacherLessonData)) {
                                                //Join lesson
                                                echo ' data-entity_type="teacher_lesson" data-entity_id="'.$teacherLessonData['teacher_lesson_id'].'"';
                                            } else {
                                                //Order a lesson
                                                echo ' data-entity_type="subject" data-entity_id="'.$subjectData['subject_id'].'"';
                                            }
                                            ?>><i class="iconMedium-mail pull-left"></i></a>
                                        <i class="iconSmall-green-dot pull-left space23"></i>
                                        <span class="pull-left online"><?php echo __('Online'); ?></span>
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



                                    <div class="space22 clear-left pull-left">
                                        <?php
                                            echo $this->Layout->ratingNew($teacherData['teacher_avarage_rating'], false, 'space3 space22');
                                        ?>
                                    </div>
                                </div> <!-- /student-main-box -->
                            </div> <!-- /pic-butn-box -->
                        </li>
                    </ul>
                    <!--<a href="#" class="more radius3 gradient2 space8"><strong><?php echo __('Load More'); ?></strong><i class="iconSmall-more-arrow"></i></a>-->

                    <?php
                        //if($subjectRatingByStudents) {
                            echo $this->element('Home'.DS.'reviews_by_students', array('ratingByStudents'=>$subjectRatingByStudents, 'title'=>__('What student say about this subject?')));
                        //}
                    ?>


                    <!--<div class="lesson-box space8">
                        <h3 class="radius1"><strong>See Other Teachers On This Subject</strong></h3>
                        <div class="box-subject radius2">
                            <?php /*if(!empty($otherTeacherForThisSubject)) { */?>
                            <a href="#" class="arrow-left arrws2"></a>
                            <ul class="subject-books subject-books1">
                            <?php
/*                                //$count = count($otherTeacherForThisSubject); $i=1;
                                foreach($otherTeacherForThisSubject AS $otfts) {
                                    //echo '<li',($i++==$count ? 'class="m-none3"' : null),'>';
                                    echo '<li>',$this->Html->image($this->Layout->image($otfts['Teacher']['image_source'], 63, 63), array('alt' => 'Topic image')),'</li>';
                                }

                            */?>
                            </ul>
                            <a href="#" class="arrow-right arrws2"></a>
                            <?php /*} */?>
                        </div>
                    </div>-->


                </div> <!-- /cont-span17 -->
            </div> <!-- /cont-span12 -->
        </div><!-- /row -->
    </div><!-- /container-inner -->
</Section>