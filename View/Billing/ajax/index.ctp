<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
    $(document).ready(function(){
        //Activate tooltip
        initToolTips();
    });
</script>

    <?php
////////////// Page 1 - start
if($page==1) {
    ?>
<script type="text/javascript">
    $(document).ready(function(){
        var url = '/Billing/index/{limit}/{page}';
        lmObj.loadMoreButton('#billing-history-load-more', 'click', '#billing-history', url, {}, 'get', <? echo $limit; ?>);
        lmObj.setItemsCountSelector('#billing-history-load-more', '#billing-history li' );
    });
</script>




<div class="fullwidth pull-left cont-span15">
    <h2><strong><?php echo __('Billing History'); ?></strong></h2>

    <div class="fullwidth pull-left space7">
        <ul id="billing-history">
            <li>
                <div class="space2 space6 left-student-box2 left-student-newbox2">
                    <h5 class="pull-left"><strong><?php echo __('Billing info'); ?></strong></h5>
                </div>
                <div class="space2 space6 right-student-box2 right-student-newbox2">
                    <h5 class=" pull-left"><strong><?php echo __('Lesson\'s info'); ?></strong></h5>
                </div>
            </li>

    <?php
}
foreach($response['response']['billingHistory'] AS $billingHistory) {
    //Link to the subject page
    $toArchive = $this->Html->link(__('Lesson page'), array('controller'=>'Home',
        'action'=> 'teacherSubject',
        $billingHistory['TeacherLesson']['subject_id']));

    ?>

    <li id="teacher_lesson_id_<?php echo $billingHistory['TeacherLesson']['teacher_lesson_id']; ?>">
        <div class="fullwidth pull-left"></div>
        <div class="lesson-box space2 left-student-box2 left-student-newbox2 pull-left">
            <div class="head-back radius1">
                <h1><?php echo $this->TimeTz->niceShort($billingHistory['TeacherLesson']['datetime']); ?></h1>
                <div class="dropdown pull-right">
                    <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                        <i class="iconSmall-drop-arrow"></i>
                    </a>
                    <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                        <li><?php echo $toArchive; ?></li>
                    </ul>
                </div>

            </div>

            <div class="lesson-box-content" id="lesson_box_<?php echo $billingHistory['TeacherLesson']['teacher_lesson_id']; ?>_msg">
                <p>
                    <strong><?php echo __('Total students'); ?>:</strong> <?php echo $billingHistory['TeacherLesson']['num_of_students']; ?><br />
                    <strong><?php echo __('Successful transactions'); ?>:</strong> <?php echo $billingHistory['TeacherLesson']['payment_success_transactions_count']; ?><br />
                    <strong><?php echo __('Student cost'); ?>:</strong> <?php echo $billingHistory['TeacherLesson']['payment_per_student_price']; ?>$<br />
                    <strong><?php echo __('Fees per student'); ?>:</strong> <?php echo $billingHistory['TeacherLesson']['payment_per_student_commission']; ?>$<br />
                </p>
            </div>

            <div class="lesson-box-footer radius2">
                <span class="pull-left space22 space3"><strong>
                <?php echo __('Total'); ?>: <?php
                    echo $billingHistory['TeacherLesson']['payment_success_transactions_count'] *
                        ($billingHistory['TeacherLesson']['payment_per_student_price'] - $billingHistory['TeacherLesson']['payment_per_student_commission']);
                ?>$</strong</span>
            </div><!-- /lesson-box-footer -->

        </div><!-- /lesson-box  -->



        <?php
        unset($billingHistory['TeacherLesson']['datetime']);
        echo $this->element('Panel/lesson_box_li', array(
            'lessonData'        => $billingHistory,
            'id'                => 'lesson_box_'.$billingHistory['TeacherLesson']['teacher_lesson_id']
        ));
        ?>
    </li>
    <?php
}

////////////// Page 1 - start
if($page==1) {
    ?>

        </ul>
        <div class="fullwidth pull-left">
            <?php
            if(count($response['response']['billingHistory'])>=$limit) {
                echo '<a href="#" class="more radius3 gradient2 space8" id="billing-history-load-more"><strong>', __('Load More') ,'</strong><i class="iconSmall-more-arrow"></i></a>';
            }
            ?>
        </div>
    </div><!-- /left-student-box-->
</div>

<?php
////////////// Page 1 - start
}