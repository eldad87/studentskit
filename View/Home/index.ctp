<?php echo $this->element('Home/search');  ?>

<div id="subjects" class="container">
<?php
if($newSubjects) {
	echo '<p>Newest subjects</p>'; 
	foreach($newSubjects AS $newSubject) {
		$newSubject['Subject']['one_on_one_price'] = $newSubject['Subject']['1_on_1_price'];
		echo $this->element('subject', $newSubject['Subject']);
	}
}
?>
</div>