<div>
<?php
echo 'avrage rating: '.$avarage_rating;
echo '<br />';
echo '#of students: '.$students_amount;
echo '<br />';
echo '#of reviews: '.$raters_amount;
echo '<br />';
if(isSet($total_teaching_minutes)) {
	echo 'total hours: '.ceil($total_teaching_minutes/MINUTE);
} else {
	echo 'total hours: '.ceil($total_lessons*$duration_minutes/MINUTE);
}
?>
</div>