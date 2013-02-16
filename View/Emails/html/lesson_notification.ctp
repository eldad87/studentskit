<h1 style="margin-top: 0px; color: #555">
    Lesson notification
</h1>
<div style="margin-left: 5px">
    <p>Dear <?php echo $fullName; ?>,</p>
    <p>We just want to let you know, that your lessons "<?php
        echo $this->Html->link($lessonData['name'], array('controller'=>'Lessons',
            'action'=>($lessonData['lesson_type']==LESSON_TYPE_LIVE ? 'index' : 'video'),
            ($lessonData['lesson_type']==LESSON_TYPE_LIVE ? $lessonData['teacher_lesson_id'] : $lessonData['subject_id']),
            'full_base'=>true
        ));
        ?>" will start in <strong><?php echo $startsInMin; ?> minutes</strong>.</p>
    <?php
        if(!$isTeacher) {
    ?>
        <p>For any problem, please contact the relevant teacher.</p>
    <?php
        }
    ?>
</div>