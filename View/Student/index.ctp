<?php echo $this->element('Panel/menu');  ?>

<h3>About to start</h3>
<?php 
foreach($upcommingLessons AS $upcommingLesson) {
	echo 'datetime: ',$upcommingLesson['UserLesson']['datetime'],'<br />';
	echo 'Name: ',$upcommingLesson['UserLesson']['name'],'<br />';
	echo '<br />';
	
	echo 'Teacher\'s name: ',$upcommingLesson['Teacher']['first_name'], ' - ',$upcommingLesson['Teacher']['last_name'],'<br />';
	echo '<br />';
	
	echo 'description: ',$upcommingLesson['UserLesson']['description'],'<br />';
	echo '<br />';
	
	echo 'Rating: ',$upcommingLesson['Subject']['avarage_rating'],'<br />';
	
	echo 'Max students: ',$upcommingLesson['UserLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$upcommingLesson['UserLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$upcommingLesson['UserLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$upcommingLesson['UserLesson']['full_group_total_price'],'<br />';
	
	echo '<br /><br /><br />';
}
?>