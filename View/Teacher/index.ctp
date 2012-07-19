<?php echo $this->element('Panel/menu');  ?>

<h3>About to start</h3>
<?php 
foreach($aboutToStartLessons AS $aboutToStartLesson) {
	echo 'datetime: ',$aboutToStartLesson['TeacherLesson']['datetime'],'<br />';
	echo 'Name: ',$aboutToStartLesson['TeacherLesson']['name'],'<br />';
	echo '<br />';
	
	echo 'Teacher\'s name: ',$aboutToStartLesson['User']['first_name'], ' - ',$aboutToStartLesson['User']['last_name'],'<br />';
	echo '<br />';
	
	echo 'description: ',$aboutToStartLesson['TeacherLesson']['description'],'<br />';
	echo '<br />';
	
	echo 'Rating: ',$aboutToStartLesson['Subject']['avarage_rating'],'<br />';
	
	echo 'Max students: ',$aboutToStartLesson['TeacherLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$aboutToStartLesson['TeacherLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$aboutToStartLesson['TeacherLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$aboutToStartLesson['TeacherLesson']['full_group_total_price'],'<br />';
	
	echo '<br /><br /><br />';
}
?>