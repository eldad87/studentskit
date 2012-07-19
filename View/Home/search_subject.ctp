<?php echo $this->element('Home/search'); ?>

<?php 
if($subjectsData) {
	echo '<p>Found subjects</p>'; 
	foreach($subjectsData AS $subjectData) {
		//pr($subjectData['Subject']);
		$subjectData['Subject']['one_on_one_price'] = $subjectData['Subject']['1_on_1_price'];
		echo $this->element('subject', $subjectData['Subject']),'<br />';
	}
}
?>

