<?php
$this->Html->scriptBlock('
    $(document).ready(function() {
        mixpanel.track("Home. user profile load");

        $(\'.msg-teacher\').click(function() {
            mixpanel.track("Home. User pm click");
        });
    });
    ', array('inline'=>false));

    echo $this->element('Panel'.DS.'send_msg_popup', array('buttonSelector'=>'.msg-user'));
?>
<!-- Containeer
================================================== -->
<Section class="container">
    <div class="container-inner">
        <div class="row">
            <div class="cont-span12">
                <div class="cont-span16 c-box-mar cbox-space pos">
                    <div class="student-main-box3 radius3">
                        <div class="student-main-inner">
                            <a title="" href="#" class="teacher-pic radius3">
                            <?php
                                echo $this->Html->image($this->Layout->image($userData['image_source'], 149, 182), array('alt' => 'User image'))
                            ?>
                            </a>
                            <p class="onliestatus">
                                <a href="#" class="msg-user requireLogin" data-to_user_id="<?php echo $userData['user_id']; ?>"><i class="iconMedium-mail pull-left"></i></a>
                                <i class="iconSmall-green-dot pull-left space23"></i>
                                <span class="pull-left online"><?php echo __('Online'); ?></span>
                            </p>
                            <div class="head-text3">
                                <div class="pull-left tutorname-wrapeper">
                                    <!--<a href="#" class="tutroaname"><span class="pad5"><strong><?php /*echo $userData['username']; */?></strong></span></a>-->
                                    <h6 class="pad8">
                                        <i class="iconSmall-flag pointer contact-request"
                                           data-subject="<?php
                                           echo __('Report on user '), $userData['user_id'];
                                           ?>"
                                           data-topic="<?php App::import('Model', 'Contact'); echo Contact::CONTACT_FLAG; ?>"></i>

                                        <?php echo $userData['username']; ?>
                                    </h6>
                                </div>
                                <!--<span class="fontsize1 pad6 pull-left">Expert Math Teacher</span>-->
                                <p class="pull-left pad8"><?php echo $userData['student_about']; ?></p>
                            </div> <!-- /head-text3-->
                            <!--<p class="pull-left ">Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source.</p>-->
                        </div>
                        <div class="icon-box-social  bod2">
                            <div class="social-icons pad2 pad8">
                                <div class="pull-left">
                                    <?php echo $this->Facebook->like(array('show_faces'=>'false', 'layout'=>'button_count')); ?>
                                </div>
                                <!--<a href="#" class="fb"></a>
                                <a href="#" class="twit"></a>
                                <a href="#" class="g-one"></a>-->
                            </div>
                        </div>
                        <div class="log-box">
                            <p class="log1 radius3 gradient2"><span class="fontsize4"><?php echo ceil($userData['students_total_learning_minutes']/60); ?> <?php echo __('H'); ?></span><br /><?php echo __('Learning'); ?></p>
                            <p class="log1 radius3 gradient2"><span class="fontsize4"><?php echo $userData['student_total_lessons']; ?></span><br /><?php echo __('Lessons'); ?></p>
                            <p class="log1 radius3 gradient2"><span class="fontsize4"><?php echo $userData['student_raters_amount']; ?></span><br><?php echo __('Reviews'); ?></p>
                            <a href="#" class="log2 btn-black radius3"><?php
                                echo $this->Layout->ratingNew($userData['student_avarage_rating'], false, 'space20 centered');
                                ?><br /><?php echo __('Rating'); ?></a>

                        </div>
                    </div>

                    <?php
                        echo $this->element('Home'.DS.'latest_lessons', array('topics'=>$archiveLessons));
                    ?>
                </div> <!-- /cont-span3 -->
                <div class="cont-span17 cbox-space ">

                    <?php
                        //if($latestPosts) {
                            echo $this->element('Home'.DS.'board_topics', array('topics'=>$latestPosts));
                        //}

                        //if($studentReviews) {
                            echo $this->element('Home'.DS.'reviews_by_teachers', array('ratingByTeachers'=>$studentReviews));
                        //}
                    ?>

                </div> <!-- /cont-span17 -->
            </div> <!-- /cont-span12 -->
        </div><!-- /row -->
    </div><!-- /container-inner -->
</Section>