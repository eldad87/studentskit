Lesson notification

Dear <?php echo $fullName; ?>,
We just want to let you know, that your lessons about "<?php $lessonData['name']; ?>" will start in <?php echo $startsInMin; ?> minutes; To join, click here:
<?php
echo Router::url(
    array('controller'=>'Lessons',
        'action'=>($lessonData['lesson_type']==LESSON_TYPE_LIVE ? 'index' : 'video'),
        ($lessonData['lesson_type']==LESSON_TYPE_LIVE ? $lessonData['teacher_lesson_id'] : $lessonData['subject_id'])
    ),
    true
);
?>
<?php
if(!$isTeacher) {
?>
    For any problem, please contact the relevant teacher.
<?php
}
?>
