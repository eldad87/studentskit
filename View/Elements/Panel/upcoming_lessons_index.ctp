<div class="message-tm-main">
    <h5 class="space2"><strong><?php echo __('Lesson About To Start'); ?></strong></h5>

    <?php
    $i = 0;
    foreach($upcomingLessons AS $bookingLesson) {
        $i++;
    ?>
    <div class="<?php echo ($i%2==0 ? 'message-tm-right' : 'message-tm-left'); ?> space8">
        <div class="lesson-box">
            <h3 class="radius1"><strong><?php echo $bookingLesson['UserLesson']['name']; ?></strong></h3>
            <div class="lesson-box-content">
                <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($bookingLesson['UserLesson']['image_source'], 72, 72),
                                                                        array('alt'=>'Lesson Image')); ?></div>
                <div class="usr-text2">
                    <h4>by <?php echo $bookingLesson['Teacher']['username']; ?></h4>
                    <p><?php echo $bookingLesson['UserLesson']['description']; ?></p>
                    <a href="#"><strong><?php echo sprintf(__('Starts on: %s'), $this->Time->niceShort($bookingLesson['UserLesson']['datetime'])); ?></strong></a>
                </div>
            </div>
            <div class="lesson-box-footer radius2">

                <div class="pull-left star"><?php echo $this->Layout->rating($bookingLesson['Subject']['avarage_rating']); ?></div>
                <div class="pull-right">
                    <?php echo $this->Layout->priceTag($bookingLesson['UserLesson']['full_group_total_price'], $bookingLesson['UserLesson']['full_group_student_price']); ?>
                    <!--<a href="#"><i class="iconSmall-info space3"></i></a>-->
                </div>
            </div><!-- /lesson-box-footer -->
        </div> <!-- /lesson-box -->
    </div><!-- /message-tm-left -->
    <?php } ?>
</div><!-- /message-tm-main -->