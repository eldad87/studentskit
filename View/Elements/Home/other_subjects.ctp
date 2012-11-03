<!-- other-subjects start -->
<div class="student-main-box radius3">
    <h5 class="fullwidth pad8"><strong>My Subjects</strong></h5>
    <div class="my-subject-box fix-height">
        <ul class="subject-box">
            <?php
                foreach($teacherSubjects AS $teacherSubject) {
                    echo $this->element('Home/other_subject_li', array('teacherSubject'=>$teacherSubject['Subject']));
                }
            ?>
        </ul>
    </div>
</div>
<a href="#" class="more radius3 gradient2 space9 pull-left mysubject-more"><strong>Load More</strong><i class="iconSmall-more-arrow"></i></a>
<!-- other-subjects ends -->
