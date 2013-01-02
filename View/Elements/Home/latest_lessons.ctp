<!-- /student-main-box -->
<div class="lesson-box pad8 space4">
    <h3 class="radius1"><strong>Latest Lessons</strong></h3>
    <div class="box-subject2 radius3">
        <div class="latest-lessons">
            <?php
            if($archiveLessons) {
                $i=0;
                foreach($archiveLessons AS $archiveLesson) {
                    echo $this->element('Home/latest_lessons_div', array('archiveLesson'=>$archiveLesson, 'first'=>!$i));
                    $i++;
                }
            }
            ?>
        </div>
    </div>
</div>
<!-- /lesson-box -->
<a href="#" class="more radius3 gradient2 space9 pull-left latest-lessons"><strong><?php echo __('Load More'); ?></strong><i class="iconSmall-more-arrow"></i></a>
