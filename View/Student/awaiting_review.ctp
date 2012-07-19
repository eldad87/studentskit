<?php echo $this->element('Panel/menu');  ?>

<h3>Student rating</h3>
<?php
echo $this->Html->link($studentAvarageRating, array('action'=>'myReviews'));
?>
<br /><br />

<?php 
if($awaitingReviews) {
	echo '<h3>Set Feedback</h3>';
	foreach($awaitingReviews AS $awaitingReview) {
		echo 'Lesson Name: ',$awaitingReview['UserLesson']['name'],'<br />';
		echo 'Date: ',$awaitingReview['UserLesson']['datetime'],'<br />';
		echo 'teacher Name: ',$awaitingReview['Teacher']['first_name'],' ',$awaitingReview['Teacher']['last_name'],'<br />';
		echo $this->Html->link('set review', array('action'=>'setReview', $awaitingReview['UserLesson']['user_lesson_id']));
		echo '<br /><br />';
	}
} else {
	echo '<h3>No Feedback</h3>';
}
?>