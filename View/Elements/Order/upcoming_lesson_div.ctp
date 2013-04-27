<div class="main-student fullwidth bod2<?php echo ($first ? ' space6' : null) ?>">
    <div class="inner-spots-box">
        <div class="pull-right">
            <?php echo $this->Layout->priceTag($upcomingAvailableLesson['price'], $upcomingAvailableLesson['bulk_price'], 'space25 order-price'); ?>
            <div class="clear"></div>
            <?php
            echo $this->Html->link('Join', array('controller'=>'Order', 'action'=>'init', 'join', $upcomingAvailableLesson['teacher_lesson_id']),
                array('class'=>'btn-color-gry move-right space35 centered space3'));
            ?>
        </div>
        <div class="space36">
            <p class="pull-left fullwidth">Start <?php echo $this->TimeTZ->niceShort($upcomingAvailableLesson['datetime']); ?></p>
            <p class="pull-left fullwidth">Current student <?php echo $upcomingAvailableLesson['num_of_students']; ?> of <?php echo $upcomingAvailableLesson['max_students']; ?></p>
            <?php echo $this->Html->link('Lesson page', array('controller'=>'Home', 'action'=>'teacherLesson', $upcomingAvailableLesson['teacher_lesson_id']));  ?>
        </div>

    </div>
</div>