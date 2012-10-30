<!-- other-subjects start -->
<div class="student-main-box radius3 fix-height">
    <h5 class="fullwidth pad8"><strong>My Subjects</strong></h5>
    <div class="my-subject-box fix-height">
        <ul class="subject-box">
        <?php
            $countTS = count($teacherSubjects);
            $i=0;
        foreach($teacherSubjects AS $teacherSubject) {
            echo '<li',(++$i==$countTS ? ' class="m-none"' : null),'>',
            $this->Html->image($this->Layout->image($teacherSubject['Subject']['image_source'], 128, 95), array('alt' => 'Topic image')),
            $this->Html->link('<strong>'.$teacherSubject['Subject']['name'].'</strong>',
                array('controller'=>'Home', 'action'=>'teacherSubject', $teacherSubject['Subject']['subject_id']),
                array('escape'=>false, 'class'=>'fontsize1')),'
                    <div class="pull-right">
                        <div class="price-tag"><span>',$this->Layout->priceTag($teacherSubject['Subject']['1_on_1_price'], $teacherSubject['Subject']['full_group_student_price']),'</span></div>
                    </div>
                </li>';
        }
        ?>
        </ul>
        <div id="more"></div>
    </div>
</div>
<a href="#" class="more radius3 gradient2 space9 pull-left mysubject-more"><strong>Load More</strong><i class="iconSmall-more-arrow"></i></a>
<!-- other-subjects ends -->
