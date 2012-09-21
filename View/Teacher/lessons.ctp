<?php echo $this->element('Panel/menu');  ?>

<h3>Upcomming</h3>
<?php
/*pr($bookingRequests);
pr($archiveLessons);
pr($lessonInvitations);
pr($pendingProposedLessons);*/
foreach($upcommingLessons AS $upcommingLesson) {
	echo 'teacher_lesson_id: ',$upcommingLesson['TeacherLesson']['teacher_lesson_id'],'<br />';
	echo 'datetime: ',$upcommingLesson['TeacherLesson']['datetime'],'<br />';
	echo 'description: ',$upcommingLesson['TeacherLesson']['description'],'<br />';
	echo 'language: ',$upcommingLesson['TeacherLesson']['language'],'<br />';
	echo 'Num of students: ',$upcommingLesson['TeacherLesson']['num_of_students'],'<br />';
	echo '<br />';
	
	echo 'Rating: ',$upcommingLesson['Subject']['avarage_rating'],'<br />';
	echo 'Max students: ',$upcommingLesson['TeacherLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$upcommingLesson['TeacherLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$upcommingLesson['TeacherLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$upcommingLesson['TeacherLesson']['full_group_total_price'],'<br />';
	echo '<br />';
	
	echo 'Message: ',$upcommingLesson['TeacherLesson']['teacher_user_id'],'-',$upcommingLesson['TeacherLesson']['student_user_id'],'-',$upcommingLesson['TeacherLesson']['teacher_lesson_id'],'<br />';
	
	echo '<br />';
	echo $this->Html->link('Cancel', array('controller'=>'Teacher','action'=>'cancelTeacherLesson', $upcommingLesson['TeacherLesson']['teacher_lesson_id'])),'<br />';
	echo $this->Html->link('Manage', array('controller'=>'Teacher','action'=>'manageTeacherLesson', $upcommingLesson['TeacherLesson']['teacher_lesson_id']));
	
	
	echo '<br /><br /><br />';
}
?>

<h3>Booking requests</h3>
<?php 
foreach($bookingRequests AS $bookingRequest) {
	echo 'user_lesson_id: ',$bookingRequest['UserLesson']['user_lesson_id'],'<br />';
	echo 'datetime: ',$bookingRequest['UserLesson']['datetime'],'<br />';
	echo 'Name: ',$bookingRequest['UserLesson']['name'],'<br />';
	echo 'description: ',$bookingRequest['UserLesson']['description'],'<br />';
	echo 'language: ',$bookingRequest['UserLesson']['language'],'<br />';
	echo '<br />';
	
	echo 'Rating: ',$bookingRequest['Subject']['avarage_rating'],'<br />';
	echo 'Max students: ',$bookingRequest['UserLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$bookingRequest['UserLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$bookingRequest['UserLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$bookingRequest['UserLesson']['full_group_total_price'],'<br />';
	echo '<br />';
	
	echo 'Message: ',$bookingRequest['UserLesson']['teacher_user_id'],'-',$bookingRequest['UserLesson']['student_user_id'],'-',$bookingRequest['UserLesson']['user_lesson_id'],'<br />';
	
	echo '<br />';
    if(empty($bookingRequest['UserLesson']['teacher_lesson_id'])) {
        echo $this->Html->link('Re-Propose', array('controller'=>'Student','action'=>'reProposeRequest', $bookingRequest['UserLesson']['user_lesson_id'])),'<br />';
    }
	echo $this->Html->link('Accept', array('controller'=>'Student','action'=>'acceptUserLesson', $bookingRequest['UserLesson']['user_lesson_id'])),'<br />';
    echo $this->Html->link('Cancel', array('controller'=>'Student','action'=>'cancelUserLesson', $bookingRequest['UserLesson']['user_lesson_id']));

	
	
	echo '<br /><br /><br />';
}
?>

<h3>Archive</h3>
<?php 
foreach($archiveLessons AS $archiveLesson) {
	echo 'teacher_lesson_id: ',$archiveLesson['TeacherLesson']['teacher_lesson_id'],'<br />';
	echo 'datetime: ',$archiveLesson['TeacherLesson']['datetime'],'<br />';
	echo 'Name: ',$archiveLesson['TeacherLesson']['name'],'<br />';
	echo 'description: ',$archiveLesson['TeacherLesson']['description'],'<br />';
	echo 'language: ',$archiveLesson['TeacherLesson']['language'],'<br />';
	echo '<br />';
	
	echo 'Rating: ',$archiveLesson['Subject']['avarage_rating'],'<br />';
	
	echo 'Max students: ',$archiveLesson['TeacherLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$archiveLesson['TeacherLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$archiveLesson['TeacherLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$archiveLesson['TeacherLesson']['full_group_total_price'],'<br />';
	
	echo '<br /><br /><br />';
}
?>

<h3>Invitations sent</h3>
<?php 
foreach($lessonInvitations AS $lessonInvitation) {
	echo 'user_lesson_id: ',$lessonInvitation['UserLesson']['user_lesson_id'],'<br />';
	echo 'Is offer: ',(empty($lessonInvitation['UserLesson']['request_subject_id']) ? 'Offer' : 'Request'),'<br />';
	
	echo 'datetime: ',$lessonInvitation['UserLesson']['datetime'],'<br />';
	echo 'Name: ',$lessonInvitation['UserLesson']['name'],'<br />';
	echo 'description: ',$lessonInvitation['UserLesson']['description'],'<br />';
	echo 'language: ',$lessonInvitation['UserLesson']['language'],'<br />';
	echo '<br />';
	
	echo 'Rating: ',$lessonInvitation['Subject']['avarage_rating'],'<br />';
	
	echo 'Max students: ',$lessonInvitation['UserLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$lessonInvitation['UserLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$lessonInvitation['UserLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$lessonInvitation['UserLesson']['full_group_total_price'],'<br />';
	echo '<br />';

	echo 'Booking: ',(in_array($lessonInvitation['UserLesson']['stage'], array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT)) ? 'Yes' : 'No'),'<br />';
	echo 'Message: ',$lessonInvitation['UserLesson']['teacher_user_id'],'-',$lessonInvitation['UserLesson']['student_user_id'],'-',$lessonInvitation['UserLesson']['user_lesson_id'],'<br />';
	
	echo '<br />';
    if(empty($lessonInvitation['UserLesson']['teacher_lesson_id'])) {
        echo $this->Html->link('Re-Propose', array('controller'=>'Student','action'=>'reProposeRequest', $lessonInvitation['UserLesson']['user_lesson_id'])),'<br />';
    }
	echo $this->Html->link('Cancel', array('controller'=>'Student','action'=>'cancelUserLesson', $lessonInvitation['UserLesson']['user_lesson_id']));
	echo '<br /><br /><br />';
}
?>

<?php
//<h3>Proposed Lessons (pending student approval)</h3>
/*foreach($pendingProposedLessons AS $pendingProposedLesson) {
	echo 'SubjectId: ',$pendingProposedLesson['UserLesson']['subject_id'],'<br />';
	echo 'Name: ',$pendingProposedLesson['UserLesson']['name'],'<br />';
	echo 'description: ',$pendingProposedLesson['UserLesson']['description'],'<br />';
	echo 'language: ',$pendingProposedLesson['UserLesson']['language'],'<br />';
	echo '<br />';
	
	//echo 'Rating: ',$pendingProposedLesson['UserLesson']['avarage_rating'],'<br />';
	echo 'Max students: ',$pendingProposedLesson['UserLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$pendingProposedLesson['UserLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$pendingProposedLesson['UserLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$pendingProposedLesson['UserLesson']['full_group_total_price'],'<br />';
	echo '<br />';

    echo 'Message: ',$pendingProposedLesson['UserLesson']['teacher_user_id'],'-',$pendingProposedLesson['UserLesson']['student_user_id'],'-',$pendingProposedLesson['UserLesson']['user_lesson_id'],'<br />';

	echo '<br />';
    if(empty($pendingProposedLesson['UserLesson']['teacher_lesson_id'])) {
        echo $this->Html->link('Re-Propose', array('controller'=>'Student','action'=>'reProposeRequest', $pendingProposedLesson['UserLesson']['user_lesson_id'])),'<br />';
    }
	echo $this->Html->link('Cancel', array('controller'=>'Teacher','action'=>'cancelUserLesson', $pendingProposedLesson['UserLesson']['user_lesson_id'])),'<br />';
	
	echo '<br /><br /><br />';
}*/
?>