<div>
	name: <?php echo $subjectData['name']; ?>
</div>
<?php
echo $this->Form->create(false, array('url'=>array('controller'=>'Home', 'action'=>'submitOrder', 'new', $subjectData['subject_id']), 'type'=>'get'));
//echo $this->Form->input('lesson_type', array('options'=>array(LESSON_TYPE_1ON1=>'One on On', LESSON_TYPE_GROUP=>'Group teaching')));
echo $this->Form->input('date', array('type'=>'datetime'));
echo $this->Form->end('order');

echo '<h3>Group Lessons</h3>';
foreach($groupLessons AS $groupLesson) {
    echo $groupLesson['num_of_students'],' of ',$groupLesson['max_students'],'<br />';
    echo 'Starts on:',$groupLesson['datetime'],'<br />';
    echo $this->Html->link('Join', array('action'=>'submitOrder', 'join', $groupLesson['teacher_lesson_id'])),'<br />';
}
echo '<br />';

pr($allLessons);
//var_dump($subjectData, $teacherUserData, $allLessons, $aalr);

?>