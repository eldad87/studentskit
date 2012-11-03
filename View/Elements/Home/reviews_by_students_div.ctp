<div class="main-student<?php echo (!$first ? ' bot2' : null); ?>">
    <div class="left-student-box">
        <?php
        echo $this->Html->image(
            $this->Layout->image($ratingByStudent['Student']['image_source'], 78, 78),
            array('alt' => 'User image', 'class'=>'border1'));

        echo $this->Html->image($this->Layout->rating($ratingByStudent['UserLesson']['rating_by_student'], false), array('alt' => 'User rating'));
        ?>
    </div>
    <div class="right-student-box">
        <div class="pad8">
            <h6 class="pull-left space10"><strong><?php echo $this->Html->link( $ratingByStudent['Student']['username'],
                array('controller'=>'Home', 'action'=>'user',
                    $ratingByStudent['UserLesson']['student_user_id']));
                ?></strong></h6>
            <em class="fontsize3">(Studied at <?php echo date('j,M,Y', strtotime($ratingByStudent['UserLesson']['datetime'])), null; ?>)</em></div>
        <p class="studeenmsg"><?php echo $ratingByStudent['UserLesson']['comment_by_student']; ?></p>
    </div>
</div>