<?php echo $this->element('Panel/menu');  ?>

<h3>Upcomming</h3>
<?php 
foreach($upcommingLessons AS $upcommingLesson) {
	echo 'user_lesson_id: ',$upcommingLesson['UserLesson']['user_lesson_id'],'<br />';
	echo 'datetime: ',$upcommingLesson['UserLesson']['datetime'],'<br />';
	echo 'Name: ',$upcommingLesson['UserLesson']['name'],'<br />';
	echo '<br />';
	
	echo 'Teacher\'s name: ',$upcommingLesson['Teacher']['first_name'], ' - ',$upcommingLesson['Teacher']['last_name'],'<br />';
	echo '<br />';
	
	echo 'description: ',$upcommingLesson['UserLesson']['description'],'<br />';
	echo '<br />';
    echo 'description: ',$upcommingLesson['UserLesson']['language'],'<br />';
	echo '<br />';
	
	echo 'Rating: ',$upcommingLesson['Subject']['avarage_rating'],'<br />';
	echo 'Max students: ',$upcommingLesson['UserLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$upcommingLesson['UserLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$upcommingLesson['UserLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$upcommingLesson['UserLesson']['full_group_total_price'],'<br />';
	echo '<br />';
	
	echo 'Message: ',$upcommingLesson['UserLesson']['teacher_user_id'],'-',$upcommingLesson['UserLesson']['student_user_id'],'-',$upcommingLesson['UserLesson']['user_lesson_id'],'<br />';
	
	echo '<br />';
	echo $this->Html->link('Cancel', array('controller'=>'Student','action'=>'cacnelUserLesson', $upcommingLesson['UserLesson']['user_lesson_id']));
	
	
	echo '<br /><br /><br />';
}
?>


<h3>Booking</h3>
<?php 
foreach($bookingRequests AS $upcommingLesson) {
	echo 'user_lesson_id: ',$upcommingLesson['UserLesson']['user_lesson_id'],'<br />';
	echo 'datetime: ',$upcommingLesson['UserLesson']['datetime'],'<br />';
	echo 'Name: ',$upcommingLesson['UserLesson']['name'],'<br />';
	echo '<br />';
	
	echo 'Teacher\'s name: ',$upcommingLesson['Teacher']['first_name'], ' - ',$upcommingLesson['Teacher']['last_name'],'<br />';
	echo '<br />';
	
	echo 'description: ',$upcommingLesson['UserLesson']['description'],'<br />';
	echo '<br />';
    echo 'language: ',$upcommingLesson['UserLesson']['language'],'<br />';
    echo '<br />';
	
	echo 'Rating: ',$upcommingLesson['Subject']['avarage_rating'],'<br />';
	echo 'Max students: ',$upcommingLesson['UserLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$upcommingLesson['UserLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$upcommingLesson['UserLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$upcommingLesson['UserLesson']['full_group_total_price'],'<br />';
	echo '<br />';
	
	echo 'Message: ',$upcommingLesson['UserLesson']['teacher_user_id'],'-',$upcommingLesson['UserLesson']['student_user_id'],'-',$upcommingLesson['UserLesson']['user_lesson_id'],'<br />';
	
	echo '<br />';
    if(empty($upcommingLesson['UserLesson']['teacher_lesson_id'])) {
        echo $this->Html->link('Re-Propose', array('controller'=>'Student','action'=>'reProposeRequest', $upcommingLesson['UserLesson']['user_lesson_id'])),'<br />';
    }
	echo $this->Html->link('Cancel', array('controller'=>'Student','action'=>'cacnelUserLesson', $upcommingLesson['UserLesson']['user_lesson_id']));
	
	
	echo '<br /><br /><br />';
}
?>

<h3>Archive</h3>
<?php 
foreach($archiveLessons AS $archiveLesson) {
	echo 'user_lesson_id: ',$archiveLesson['UserLesson']['user_lesson_id'],'<br />';
	echo 'datetime: ',$archiveLesson['UserLesson']['datetime'],'<br />';
	echo 'Name: ',$archiveLesson['UserLesson']['name'],'<br />';
	echo '<br />';
	
	echo 'Teacher\'s name: ',$archiveLesson['Teacher']['first_name'], ' - ',$archiveLesson['Teacher']['last_name'],'<br />';
	echo '<br />';
	
	echo 'description: ',$archiveLesson['UserLesson']['description'],'<br />';
	echo '<br />';
    echo 'language: ',$archiveLesson['UserLesson']['language'],'<br />';
    echo '<br />';
	
	echo 'Rating: ',$archiveLesson['Subject']['avarage_rating'],'<br />';
	
	echo 'Max students: ',$archiveLesson['UserLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$archiveLesson['UserLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$archiveLesson['UserLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$archiveLesson['UserLesson']['full_group_total_price'],'<br />';
	
	echo '<br /><br /><br />';
}
?>

<h3>Invitations</h3>
<?php 
foreach($lessonInvitations AS $lessonInvitation) {
	echo 'user_lesson_id: ',$lessonInvitation['UserLesson']['user_lesson_id'],'<br />';
	echo 'Is offer: ',(empty($lessonInvitation['UserLesson']['request_subject_id']) ? 'Offer' : 'Request'),'<br />';
	
	echo 'datetime: ',$lessonInvitation['UserLesson']['datetime'],'<br />';
	echo 'Name: ',$lessonInvitation['UserLesson']['name'],'<br />';
	echo '<br />';
	
	echo 'Teacher\'s name: ',$lessonInvitation['Teacher']['first_name'], ' - ',$lessonInvitation['Teacher']['last_name'],'<br />';
	echo '<br />';
	
	echo 'description: ',$lessonInvitation['UserLesson']['description'],'<br />';
	echo '<br />';
	echo 'language: ',$lessonInvitation['UserLesson']['language'],'<br />';
	echo '<br />';
	
	echo 'Rating: ',$lessonInvitation['Subject']['avarage_rating'],'<br />';
	
	echo 'Max students: ',$lessonInvitation['UserLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$lessonInvitation['UserLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$lessonInvitation['UserLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$lessonInvitation['UserLesson']['full_group_total_price'],'<br />';
	echo '<br />';
	
	echo 'Booking: ',($lessonInvitation['UserLesson']['stage']==USER_LESSON_PENDING_TEACHER_APPROVAL ? 'Yes' : 'No'),'<br />';
	echo 'Message: ',$lessonInvitation['UserLesson']['teacher_user_id'],'-',$lessonInvitation['UserLesson']['student_user_id'],'-',$lessonInvitation['UserLesson']['user_lesson_id'],'<br />';
	
	echo '<br />';
    if(empty($lessonInvitation['UserLesson']['teacher_lesson_id'])) {
        echo $this->Html->link('Re-Propose', array('controller'=>'Student','action'=>'reProposeRequest', $lessonInvitation['UserLesson']['user_lesson_id'])),'<br />';
    }
	echo $this->Html->link('Accept', array('controller'=>'Student','action'=>'acceptUserLesson', $lessonInvitation['UserLesson']['user_lesson_id'])), '<br /><br />';
	echo $this->Html->link('Cancel', array('controller'=>'Student','action'=>'cacnelUserLesson', $lessonInvitation['UserLesson']['user_lesson_id']));
	echo '<br /><br /><br />';
}
?>

<h3>Subject Requests</h3>
<?php
echo $this->Html->link('Make request', array('controller'=>'Requests','action'=>'makeRequest')),'<br />';
foreach($subjectRequests AS $subjectRequest) {
	echo 'SubjectId: ',$subjectRequest['Subject']['subject_id'],'<br />';
	echo 'Name: ',$subjectRequest['Subject']['name'],'<br />';
	echo 'description: ',$subjectRequest['Subject']['description'],'<br />';
    echo 'language: ',$subjectRequest['Subject']['language'],'<br />';
	echo 'Enable: ',$subjectRequest['Subject']['is_enable'],'<br />';
	echo '<br />';
	
	echo 'Rating: ',$subjectRequest['Subject']['avarage_rating'],'<br />';
	echo 'Max students: ',$subjectRequest['Subject']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$subjectRequest['Subject']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$subjectRequest['Subject']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$subjectRequest['Subject']['full_group_total_price'],'<br />';
	echo '<br />';
	
	echo 'Message: ',$subjectRequest['Subject']['user_id'],'-',$subjectRequest['Subject']['subject_id'],'<br />';

	echo '<br />';
	echo $this->Html->link('Disable', array('controller'=>'Teacher','action'=>'disableSubject', $subjectRequest['Subject']['subject_id']));
	
	echo '<br /><br /><br />';
}
?>