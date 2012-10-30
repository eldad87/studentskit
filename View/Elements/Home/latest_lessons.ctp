<!-- /student-main-box -->
<div class="lesson-box pad8 space4">
    <h3 class="radius1"><strong>Latest Lessons</strong></h3>
    <div class="box-subject2 radius3">
        <div class="studnt-page-scorll1">
            <?php
            if($archiveLessons) {
                $countAL = count($archiveLessons);
                $i=0;

                foreach($archiveLessons AS $archiveLesson) {
                    ?>
                    <div class="main-student<?php echo (++$i==$countAL ? null : ' bod2') ?>">
                        <div class="left-student-box">
                            <?php
                            echo $this->Html->image($this->Layout->image($archiveLesson['UserLesson']['image_source'], 58, 58), array('alt' => 'Lesson image', 'class'=>'border1'));
                            echo $this->Html->image($this->Layout->rating($archiveLesson['UserLesson']['rating_by_teacher'], false), array('alt' => 'User lesson rating'));
                            ?>
                        </div>
                        <div class="right-student-box">
                            <div class="pad8">
                                <h6 class="pull-left space10"><strong><?php echo $archiveLesson['UserLesson']['name']; ?></strong></h6>
                                <em class="fontsize1 space31">(Studied at <?php echo $archiveLesson['UserLesson']['datetime']; ?>)</em></div>
                            <p class="studeenmsg"><?php echo $archiveLesson['UserLesson']['description']; ?></p>
                        </div>
                    </div>


                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>
<!-- /lesson-box -->
<a href="#" class="more radius3 gradient2 space9 pull-left studnt-page-scorll-2"><strong>Load More</strong><i class="iconSmall-more-arrow"></i></a>
