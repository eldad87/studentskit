<div class="student-main-box radius3 space25 fix-height">
    <h5 class="fullwidth pad8 pull-left"><strong>Upcoming group lessons</strong></h5>
    <div class="up-coming">
        <ul class="subject-morelesson upcoming-more">
			<?php
            foreach($upcomingAvailableLessons AS $upcomingAvailableLesson) {
                echo $this->element('Home/upcoming_lesson_li', array('upcomingAvailableLesson'=>$upcomingAvailableLesson));
            }
			?>
        </ul>
    </div>
</div>
<a href="#" class="more radius3 gradient2 space9 pull-left upcoming-more"><strong><?php echo __('Load More'); ?></strong><i class="iconSmall-more-arrow"></i></a>



