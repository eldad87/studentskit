<!-- reviews -->
<div class="lesson-box pad8">
    <h3 class="radius1"><strong><?php echo $title; ?></strong></h3>
    <div class="box-subject2 radius3">


        <?php
        if($ratingByStudents) {
            $countSRBS = count($ratingByStudents);
            $i=0;
            foreach($ratingByStudents AS $ratingByStudent) { ?>

                <div class="main-student<?php echo (++$i==$countSRBS ? null : ' bod2') ?>">
                    <div class="left-student-box">
                        <?php
                        echo $this->Html->image(
                            $this->Layout->image($ratingByStudent['Student']['image_source'], 78, 78),
                            array('alt' => 'User image', 'class'=>'border1'));

                        echo $this->Html->image($this->Layout->rating($ratingByStudent['UserLesson']['rating_by_student'], false), array('alt' => 'User rating'));
                        ?>
                    </div>
                    <div class="right-student-box">
                        <div class="pad8"><h6 class="pull-left space10"><strong><?php echo $ratingByStudent['Student']['username']; ?></strong></h6><em class="fontsize1">(Studied at <?php echo $ratingByStudent['UserLesson']['datetime']; ?>)</em></div>
                        <p><?php echo $ratingByStudent['UserLesson']['comment_by_student']; ?></p>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>
<!-- /reviews -->