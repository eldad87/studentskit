<div>
<?php
echo 'Name: '.$first_name,' ',$last_name;
echo '<br />';
echo 'About: '.$teacher_about;
echo '<br />';
if(isSet($teacher_total_teaching_minutes)) {
	echo 'Total teaching minutes: '.$teacher_total_teaching_minutes;
	echo '<br />';
}
if(isSet($teacher_students_amount)) {
	echo 'Total students: '.$teacher_students_amount;
	echo '<br />';
}
if(isSet($teacher_raters_amount)) {
	echo 'Total students: '.$teacher_raters_amount;
	echo '<br />';
}
echo 'average total rating: '.$teacher_average_rating;
echo '<br />';
echo 'Image: '.$image;
?>
</div>