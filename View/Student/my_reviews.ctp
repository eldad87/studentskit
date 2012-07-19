<?php echo $this->element('Panel/menu');  ?>

<h3>My Reviews</h3>
<?php 
if($studentReviews) {
	echo '<br /><br /><h3>Student Reviews</h3>';
	foreach($studentReviews AS $review) {
		echo $this->element('teacher_review', $review['UserLesson']);
		echo '<br /> ';
	}
}
?>