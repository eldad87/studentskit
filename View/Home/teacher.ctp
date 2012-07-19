<?php
//Profile
//Rating
//Teacher subjects
//reviews 
echo '<br /><br /><h3>Profile</h3>';
echo $this->element('profile', $teacherUserData);

if($teacherSubjects) {
	echo '<br /><br /><h3>Teacher subjects</h3>';
	foreach($teacherSubjects AS $teacherSubject) {
		$teacherSubject['Subject']['one_on_one_price'] = $teacherSubject['Subject']['1_on_1_price'];
		echo $this->element('subject', $teacherSubject['Subject']);
		echo '<br /> ';
	}
}

if($teacherReviews) {
	echo '<br /><br /><h3>Teacher Reviews</h3>';
	foreach($teacherReviews AS $teacherReview) {
		echo $this->element('student_review', $teacherReview['UserLesson']);
		echo '<br /> ';
	}
}
?>