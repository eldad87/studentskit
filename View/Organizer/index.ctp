<section class="container">
    <div class="container-inner">
        <div class="row">
            <div class="cont-span12">
                <div class="cont-span3 c-box-mar cbox-space">
                    <div class="student-main-box radius3">
                        <a class="student-pic radius3" href="#" title="">
                            <?php echo $this->Html->image($this->Layout->image($user['image_source'], 200, 210)); ?>
                        </a>
                        <h5><?php echo $user['username']; ?></h5>
                    </div> <!-- /student-main-box -->

                    <ul class="right-menu">
                        <li class="bg-main bg-active"><a href="#" class="load2" rel="<?php echo Router::url(array('controller'=>'Student','action'=>'index')); ?>">User Management</a></li>
                        <li class="bg-sub"><a href="#" class="load2 " rel="<?php echo Router::url(array('controller'=>'Student','action'=>'lessons')); ?>">Lessons</a></li>
                        <li class="bg-sub"><a href="#" class="load2" rel="<?php echo Router::url(array('controller'=>'Student','action'=>'profile')); ?>">Profile</a></li>
                        <li class="bg-sub"><a href="#" class="load1" rel="student-profile-um-rate-students-form.html">Rate (3)</a></li>
                        <li class="bg-main"><a href="#" class="load1" rel="student-profile-tm.html">Teacher Management</a></li>
                        <li class="bg-sub"><a href="#" class="load1" rel="student-profile-tm-mysubjects.html">My Subjects</a></li>
                        <li class="bg-sub"><a href="#" class="load1" rel="student-profile-tm-mysubjects-lessons-upcoming.html">Lessons</a></li>
                        <li class="bg-sub"><a href="#" class="load1" rel="student-profile-tm-profile.html">Profile</a></li>
                        <li class="bg-sub"><a href="#" class="load1" rel="student-profile-tm-rate-form.html">Rate</a></li>
                        <li class="bg-main"><a href="#" class="load1" rel="student-profile-billinginfo.html">Billing Info</a></li>
                        <li class="bg-main"><a href="#" class="load1" rel="student-profile-calender.html">Calender</a></li>
                        <li class="bg-main"><a href="#" class="load1" rel="student-profile-tm-credits.html">Credit</a></li>
                    </ul> <!-- /right-menu -->
                </div> <!-- /cont-span3 -->
                <div class="cont-span15 c-mar-message loadpage1" id="main-area">

                </div><!-- /loadpage -->
            </div> <!-- /cont-span12 -->
        </div> <!-- /row -->
    </div> <!-- /container-inner -->
</section>