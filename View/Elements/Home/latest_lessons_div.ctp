
<div class="main-student<?php echo ($first ? null : ' bot2'); ?>">
    <div class="left-student-box">
        <?php
        echo $this->Html->image($this->Layout->image($archiveLesson['UserLesson']['image_source'], 58, 58), array('alt' => 'Lesson image', 'class'=>'border1'));
        echo $this->Layout->ratingNew($archiveLesson['UserLesson']['rating_by_teacher'], false, 'pull-left pad8');
        ?>
    </div>
    <div class="right-student-box">
        <div class="pad8">
            <h6 class="pull-left space10"><strong><?php echo $archiveLesson['UserLesson']['name']; ?></strong></h6>
            <em class="fontsize1 space31">(Studied at <?php echo $archiveLesson['UserLesson']['datetime']; ?>)</em></div>
        <p class="studeenmsg"><?php echo $archiveLesson['UserLesson']['description']; ?></p>
    </div>
</div>
