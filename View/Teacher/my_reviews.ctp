<?php echo $this->element('Panel/menu');  ?>
<?php 
if($teacherReviews) {
	echo '<br /><br /><h3>Student Reviews</h3>';
	foreach($teacherReviews AS $review) {
		echo $this->element('student_review', $review['UserLesson']);
		echo '<br /> ';
	}
} else {
	echo '<h3>No Reviews</h3>';
}
?>