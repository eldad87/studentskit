<div>
	name: <?php echo $subjectData['name']; ?>
</div>
<?php
echo $this->Form->create('UserLesson', array('url'=>array('controller'=>'Home', 'action'=>'submitOrder', 'new', $subjectData['subject_id']), 'type'=>'get'));
//echo $this->Form->input('lesson_type', array('options'=>array(LESSON_TYPE_1ON1=>'One on On', LESSON_TYPE_GROUP=>'Group teaching')));
if($isLiveLesson) {
    echo $this->Form->input('datetime', array('type'=>'datetime'));
}
echo $this->Form->end('order');

if($isLiveLesson) {
    echo '<h3>Group Lessons</h3>';
    foreach($groupLessons AS $groupLesson) {
        echo $groupLesson['num_of_students'],' of ',$groupLesson['max_students'],'<br />';
        echo 'Starts on:',$groupLesson['datetime'],'<br />';
        echo $this->Html->link('Join', array('action'=>'teacherLesson', $groupLesson['teacher_lesson_id'])),'<br />';
    }
    echo '<br />';

    echo '<h3>All Live Lessons</h3>';
    pr($allLiveLessons);
}
//var_dump($subjectData, $teacherUserData, $allLessons, $aalr);

?>