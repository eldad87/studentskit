<?php echo $this->element('Panel/menu');  ?>

<h3>Subject Lessons</h3>
<?php 
echo $this->Html->link('Create Lesson', array('controller'=>'teacher', 'action'=>'createTeacherLesson', $subjectId)),'<br /><br />';

if($nextLessons) {
	foreach($nextLessons AS $nextLesson) {
		$nextLesson = $nextLesson['TeacherLesson'];
		
		echo 'Datetime: '		,$nextLesson['datetime']					,'<br />';
		echo 'Max Students: '	,$nextLesson['max_students']				,'<br />';
		echo 'Students: '		,$nextLesson['num_of_students']				,'<br />';
		echo 'Join requests: '	,$nextLesson['num_of_pending_join_requests'],'<br />';
		echo 'Invitations: '	,$nextLesson['num_of_pending_invitations']	,'<br />';
		echo $this->Html->link('Cancel Lesson', array('controller'=>'teacher', 'action'=>'cacnelTeacherLesson', $nextLesson['teacher_lesson_id'])),'<br />';
		echo $this->Html->link('Manage Lesson', array('controller'=>'teacher', 'action'=>'manageTeacherLesson', $nextLesson['teacher_lesson_id'])),'<br /><br />';
	}
} else {
	echo 'No lessons';
}
?>
	
