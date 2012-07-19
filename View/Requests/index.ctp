<?php echo $this->element('Home/search', array('controller'=>'Requests'));  ?>

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