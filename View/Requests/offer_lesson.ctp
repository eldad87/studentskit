<?php echo $this->element('Panel/menu');  ?>

<h3>Datetime</h3>
<?php 
echo $this->Form->create('UserLesson', array('type' => 'file'));
echo $this->Form->hidden('request_subject_id', array('value'=>$requestSubjectId));
echo $this->Form->input('subject_id', array('options'=>$teacherSubjectsSuggestions));
if($isLiveLesson) {
    echo $this->Form->input('datetime', array('type'=>'datetime'));
}
echo $this->Form->submit('Save');
echo $this->Form->end();
if($isLiveLesson) {
?>
<h3>User Live Lessons</h3>
<?php var_dump($allLiveLessons);
}?>
<br />
<h3>Subjects request data</h3>
<?php var_dump($subjectData); ?>
<br />
<h3>Subjects drop down data</h3>
<?php var_dump($teacherSubjectsData); ?>