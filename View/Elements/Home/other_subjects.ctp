<!-- other-subjects start -->
<div class="student-main-box radius3">
    <h5 class="fullwidth pad8"><strong><?php echo __('My Subjects'); ?></strong></h5>
    <div class="my-subject-box">
        <ul class="subject-box">
            <?php
                foreach($teacherSubjects AS $teacherSubject) {
                    echo $this->element('Home/other_subject_li', array('teacherSubject'=>$teacherSubject['Subject'], 'lessonType'=>$teacherSubject['Subject']['lesson_type']));
                }
            ?>
        </ul>
        <?php
            if(!$teacherSubjects) {
                echo '<p>',__('No more subjects'),'</p>';
            }
        ?>
    </div>
</div>

<?php
if(count($teacherSubjects)>=$teacherOtherSubjectsLimit) {
    echo '<a href="#" class="more radius3 gradient2 space9 pull-left mysubject-more"><strong>',__('Load More'),'</strong><i class="iconSmall-more-arrow"></i></a>';
}
?>
<!-- other-subjects ends -->
