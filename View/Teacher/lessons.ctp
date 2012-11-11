<script type="text/javascript">
    //Init tabs
    $(document).ready(function(){
        //initTabsOld();
        initTabs();
    });
</script>
<div class="cont-span15 cbox-space cbox-space">
    <div class="search-all2 sort-mar">
        <div class="black-line-approv"></div>
        <ul class="booking-nav f-pad-norml um-upcoming f-pad-norml1 tab-menu">
            <li class="active"><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Teacher','action'=>'lessonsUpcoming')); ?>">Upcoming</a></li>
            <li><a href="#"  class="load3" rel="<?php echo Router::url(array('controller'=>'Teacher','action'=>'lessonsArchive')); ?>">Archive</a></li>
            <li><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Teacher','action'=>'lessonsBooking')); ?>">Booking</a></li>
            <li class="c-mar3"><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Teacher','action'=>'lessonsInvitations')); ?>">Invitations</a></li>
        </ul>
    </div>
    <div class="clear"></div>
    <div class="fullwidth loadpage">

    </div> <!-- /fullwidth -->
</div> <!-- /cont-span6 -->

<?php /*echo $this->element('Panel/menu');  */?><!--

<h3>Upcoming</h3>
<?php
/*foreach($upcomingLessons AS $upcomingLesson) {
	echo 'teacher_lesson_id: ',$upcomingLesson['TeacherLesson']['teacher_lesson_id'],'<br />';
	echo 'datetime: ',$upcomingLesson['TeacherLesson']['datetime'],'<br />';
	echo 'description: ',$upcomingLesson['TeacherLesson']['description'],'<br />';
	echo 'language: ',$upcomingLesson['TeacherLesson']['language'],'<br />';
	echo 'Num of students: ',$upcomingLesson['TeacherLesson']['num_of_students'],'<br />';
	echo '<br />';
	
	echo 'Rating: ',$upcomingLesson['Subject']['avarage_rating'],'<br />';
	echo 'Max students: ',$upcomingLesson['TeacherLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$upcomingLesson['TeacherLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$upcomingLesson['TeacherLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$upcomingLesson['TeacherLesson']['full_group_total_price'],'<br />';
	echo '<br />';
	
	echo 'Message: ',$upcomingLesson['TeacherLesson']['teacher_user_id'],'-',$upcomingLesson['TeacherLesson']['student_user_id'],'-',$upcomingLesson['TeacherLesson']['teacher_lesson_id'],'<br />';
	
	echo '<br />';
	echo $this->Html->link('Cancel', array('controller'=>'Teacher','action'=>'cancelTeacherLesson', $upcomingLesson['TeacherLesson']['teacher_lesson_id'])),'<br />';
	echo $this->Html->link('Manage', array('controller'=>'Teacher','action'=>'manageTeacherLesson', $upcomingLesson['TeacherLesson']['teacher_lesson_id']));
	
	
	echo '<br /><br /><br />';
}
*/?>

<h3>Booking requests</h3>
<?php
/*foreach($bookingRequests AS $bookingRequest) {
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
*/?>

<h3>Archive</h3>
<?php
/*foreach($archiveLessons AS $archiveLesson) {
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
*/?>

<h3>Invitations sent</h3>
--><?php
/*foreach($lessonInvitations AS $lessonInvitation) {
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
*/?>