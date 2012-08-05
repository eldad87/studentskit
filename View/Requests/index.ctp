<?php echo $this->element('Home/search', array('controller'=>'Requests'));  ?>
<?php echo $this->Html->link('Make request', array('controller'=>'Requests', 'action'=>'makeRequest')); ?><br /><br />
<div id="subjects" class="container">
<?php
if($newSubjects) {
	echo '<p>Newest Lesson requests</p>'; 
	foreach($newSubjects AS $newSubject) {
		echo $this->element('subject_request', $newSubject);
	}
}
?>
</div>