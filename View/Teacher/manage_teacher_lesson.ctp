<?php echo $this->element('Panel/menu');  ?>

<h3>Manage teacher lesson</h3>
<?php 
echo 'Subject: ',$teacherLesson['subject_id'],'<br />';
echo 'Max students: ',$teacherLesson['max_students'],'<br />';
echo '<br />';
	
foreach( $allStudents AS $studentType=>$students) {
	echo '<h3>'.$studentType.'</h3>';
	foreach($students AS $student) {
			echo 'Name: ',$student['Student']['first_name'],' - ',$student['Student']['last_name'],'<br />';
			echo 'Image: ',$student['Student']['image'],'<br />';
			echo 'Total lessons: ',$student['Student']['student_total_lessons'],'<br />';
			echo 'Rating: ',$student['Student']['student_avarage_rating'],'<br />';
			echo $this->Html->link('Cancel', array('controller'=>'Student', 'action'=>'cacnelUserLesson', $student['UserLesson']['user_lesson_id'])),'<br />';
			if($studentType=='join_reuests') {
				echo $this->Html->link('Accept', array('controller'=>'Student', 'action'=>'acceptUserLesson', $student['UserLesson']['user_lesson_id'])),'<br />';
			}
			
			echo '<br />';
	}
	echo '<br /><br /><br />';
}
?>

