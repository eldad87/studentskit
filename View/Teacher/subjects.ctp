<?php echo $this->element('Panel'.DS.'menu');  ?>


<h3>Teacher Image</h3>
<?php echo $teacherImage; ?>
<br />
<?php echo $this->Html->link('Add', array('action'=>'subject')); ?>
<br />
<br />

<h3>Subjects</h3>
<?php
foreach($subjects AS $subject) {
	$subject = $subject['Subject'];
	
	
	echo 'Name: ',$subject['name'],'<br />';
	echo 'Description: ',$subject['description'],'<br />';
	echo 'Language: ',$subject['language'],'<br />';
	echo 'Enable: ',$subject['is_enable'],'<br />';
	echo 'Public: ',$subject['is_public'],'<br />';
	echo 'Image: ',$subject['image'],'<br />';
	echo 'average_rating: ',$subject['average_rating'],'<br />';
	echo '1_on_1_price: ',$subject['1_on_1_price'],'<br />';
	echo 'max_students: ',$subject['max_students'],'<br />';
	echo 'full_group_student_price: ',$subject['full_group_student_price'],'<br />';
	echo 'full_group_total_price: ',$subject['full_group_total_price'],'<br />';
	echo $this->Html->link('Create lesson', array('controller'=>'Teacher','action'=>'createTeacherLesson', $subject['subject_id'])),'<br />';
	echo $this->Html->link('Edit', array('action'=>'Subject', $subject['subject_id'])),'<br />';
	echo $this->Html->link('Disable', array('controller'=>'Teacher','action'=>'disableSubject', $subject['subject_id']));
	echo '<br /><br /><br />';
}
?>