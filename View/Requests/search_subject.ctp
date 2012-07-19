<?php echo $this->element('Home/search'); ?>

<?php 
if($subjectsData) {
	echo '<p>Found subjects</p>'; 
	foreach($subjectsData AS $subjectData) {
		//pr($subjectData['Subject']);
		echo $this->element('subject_request', $subjectData),'<br />';
	}
}
?>