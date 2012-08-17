<h3>Subject</h3>
<?php 
echo $this->Form->create('Subject', array('type' => 'file'));
echo $this->Form->hidden('subject_id');
echo $this->Form->input('name');
echo $this->Form->input('description', array('type'=>'textarea'));
echo $this->Form->input('subject_category_id', array('options'=>$subjectCategories));

echo $this->Form->input('language', array('options'=>$languages));
echo $this->Form->input('lesson_type', array('options'=>array(LESSON_TYPE_LIVE=>'live', LESSON_TYPE_VIDEO=>'video')));
echo $this->Form->input('duration_minutes');

//echo $this->Form->input('category_id');
//echo $this->Form->input('catalog_id');
?>
<br />
<h3>Pricing</h3>
<?php 
//pr($this->data['Subject']);
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
if(isSet($nextLessons)) {
	echo '<h3>Lessons</h3>';
	pr($nextLessons);
}
?>
<br />
<?php 
if(isSet($fileSystem)) {
	echo '<h3>FileSystem</h3>';
	pr($fileSystem);
}
?>
<br />
<?php 
if(isSet($tests)) {
	echo '<h3>Test</h3>';
	pr($tests);
}
?>

