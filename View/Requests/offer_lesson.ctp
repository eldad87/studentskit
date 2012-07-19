<?php echo $this->element('Panel/menu');  ?>

<h3>Datetime</h3>
<?php 
echo $this->Form->create('TeacherLesson', array('type' => 'file'));
echo $this->Form->input('date', array('type'=>'datetime'));
echo '<br />';
echo '<br /><h3>Subject original parameters</h3>';

echo $this->Form->hidden('subject_id');
echo $this->Form->input('name');
echo $this->Form->input('description', array('type'=>'textarea'));

echo $this->Form->input('lesson_type', array('options'=>array(LESSON_TYPE_LIVE=>'live', LESSON_TYPE_VIDEO=>'video')));
//echo $this->Form->input('is_enable', array('options'=>array(1=>'on', 0=>'off')));
//echo $this->Form->input('is_public', array('options'=>array(1=>'on', 0=>'off')));
echo $this->Form->input('duration_minutes');

//echo $this->Form->input('category_id');
//echo $this->Form->input('catalog_id');
?>
<br />
<h3>Pricing</h3>
<?php
echo $this->Form->input('1_on_1_price');


echo $this->Form->input('max_students');
echo $this->Form->input('full_group_total_price');
if(isSet($groupPrice)) {
	echo 'Full Group Student Price: ',$groupPrice,'<br />';
}

echo $this->Form->submit('Save');
echo $this->Form->end();
?>
<br />
<?php 
if(isSet($subjectData)) {
	echo '<h3>Subject</h3>';
	pr($subjectData);
}
?>
<br />
<?php 
if(isSet($studentUserData)) {
	echo '<h3>studentUserData</h3>';
	pr($studentUserData);
}
?>
<br />
<?php 
if(isSet($allLessons)) {
	echo '<h3>allLessons</h3>';
	pr($allLessons);
}
?>

