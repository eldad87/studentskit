<div class="cont-span19 cbox-space">
    <!--<p class="fontsize1 space8">Your next lesson is about to start in HH:MM:SS</p>-->
    <div class="cont-span15 c-mar-message">

    <?php echo $this->element('Panel/latest_board_msg_index', array('latestUpdatedTopics'=>$latestUpdatedTopics)); ?>
    <?php echo $this->element('Panel/upcoming_lessons_index', array('upcomingLessons'=>$upcomingLessons)); ?>

    </div>
</div>