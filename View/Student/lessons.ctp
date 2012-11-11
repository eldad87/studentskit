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
            <li class="active"><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Student','action'=>'lessonsUpcoming')); ?>">Upcoming</a></li>
            <li><a href="#"  class="load3" rel="<?php echo Router::url(array('controller'=>'Student','action'=>'lessonsArchive')); ?>">Archive</a></li>
            <li><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Student','action'=>'lessonsBooking')); ?>">Booking</a></li>
            <li><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Student','action'=>'lessonsInvitations')); ?>">Invitations</a></li>
            <li class="c-mar3"><a href="#" class="load3" rel="<?php echo Router::url(array('controller'=>'Student','action'=>'subjectRequests')); ?>">Requests</a></li>
        </ul>
    </div>
    <div class="clear"></div>

    <!--    <a class="black-cent-butn2 long-wid2 fontsize1" href="#">Booking</a>
<a class="black-cent-butn2 long-wid2 fontsize1" href="#">Invitations</a>-->
    <div class="clear"></div>
    <div class="fullwidth loadpage">

    </div> <!-- /fullwidth -->
    <!-- /add-sub -->
</div> <!-- /cont-span6 -->
<!--<div class="cont-span3 sort-wid c-box-mar2 banner-visibility">
    <div class="banner-box"></div>
</div>-->


<?php /*echo $this->element('Panel/menu');  */?><!--

<h3>Upcoming</h3>
<?php /*
foreach($upcomingLessons AS $upcomingLesson) {
	echo 'user_lesson_id: ',$upcomingLesson['UserLesson']['user_lesson_id'],'<br />';
	echo 'datetime: ',$upcomingLesson['UserLesson']['datetime'],'<br />';
	echo 'Name: ',$upcomingLesson['UserLesson']['name'],'<br />';
	echo '<br />';

	echo 'Teacher\'s name: ',$upcomingLesson['Teacher']['first_name'], ' - ',$upcomingLesson['Teacher']['last_name'],'<br />';
	echo '<br />';

	echo 'description: ',$upcomingLesson['UserLesson']['description'],'<br />';
	echo '<br />';
    echo 'description: ',$upcomingLesson['UserLesson']['language'],'<br />';
	echo '<br />';

	echo 'Rating: ',$upcomingLesson['Subject']['avarage_rating'],'<br />';
	echo 'Max students: ',$upcomingLesson['UserLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$upcomingLesson['UserLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$upcomingLesson['UserLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$upcomingLesson['UserLesson']['full_group_total_price'],'<br />';
	echo '<br />';

	echo 'Message: ',$upcomingLesson['UserLesson']['teacher_user_id'],'-',$upcomingLesson['UserLesson']['student_user_id'],'-',$upcomingLesson['UserLesson']['user_lesson_id'],'<br />';

	echo '<br />';
	echo $this->Html->link('Cancel', array('controller'=>'Student','action'=>'cancelUserLesson', $upcomingLesson['UserLesson']['user_lesson_id']));


	echo '<br /><br /><br />';
}
*/?>


<h3>Booking</h3>
<?php /*
foreach($bookingRequests AS $upcomingLesson) {
	echo 'user_lesson_id: ',$upcomingLesson['UserLesson']['user_lesson_id'],'<br />';
	echo 'datetime: ',$upcomingLesson['UserLesson']['datetime'],'<br />';
	echo 'Name: ',$upcomingLesson['UserLesson']['name'],'<br />';
	echo '<br />';

	echo 'Teacher\'s name: ',$upcomingLesson['Teacher']['first_name'], ' - ',$upcomingLesson['Teacher']['last_name'],'<br />';
	echo '<br />';

	echo 'description: ',$upcomingLesson['UserLesson']['description'],'<br />';
	echo '<br />';
    echo 'language: ',$upcomingLesson['UserLesson']['language'],'<br />';
    echo '<br />';

	echo 'Rating: ',$upcomingLesson['Subject']['avarage_rating'],'<br />';
	echo 'Max students: ',$upcomingLesson['UserLesson']['max_students'],'<br />';
	echo 'Price for 1 on 1: ',$upcomingLesson['UserLesson']['1_on_1_price'],'<br />';
	echo 'Price for student: ',$upcomingLesson['UserLesson']['full_group_student_price'],'<br />';
	echo 'Price for full group: ',$upcomingLesson['UserLesson']['full_group_total_price'],'<br />';
	echo '<br />';

	echo 'Message: ',$upcomingLesson['UserLesson']['teacher_user_id'],'-',$upcomingLesson['UserLesson']['student_user_id'],'-',$upcomingLesson['UserLesson']['user_lesson_id'],'<br />';

	echo '<br />';
    if(empty($upcomingLesson['UserLesson']['teacher_lesson_id'])) {
        echo $this->Html->link('Re-Propose', array('controller'=>'Student','action'=>'reProposeRequest', $upcomingLesson['UserLesson']['user_lesson_id'])),'<br />';
    }
	echo $this->Html->link('Cancel', array('controller'=>'Student','action'=>'cancelUserLesson', $upcomingLesson['UserLesson']['user_lesson_id']));


	echo '<br /><br /><br />';
}
*/?>

<h3>Archive</h3>
<?php /*
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
*/?>

<h3>Invitations</h3>
<?php /*
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

	echo 'Booking: ',( in_array($lessonInvitation['UserLesson']['stage'], array(USER_LESSON_PENDING_TEACHER_APPROVAL, USER_LESSON_RESCHEDULED_BY_STUDENT)) ? 'Yes' : 'No'),'<br />';
	echo 'Message: ',$lessonInvitation['UserLesson']['teacher_user_id'],'-',$lessonInvitation['UserLesson']['student_user_id'],'-',$lessonInvitation['UserLesson']['user_lesson_id'],'<br />';

	echo '<br />';
    if(empty($lessonInvitation['UserLesson']['teacher_lesson_id'])) {
        echo $this->Html->link('Re-Propose', array('controller'=>'Student','action'=>'reProposeRequest', $lessonInvitation['UserLesson']['user_lesson_id'])),'<br />';
    }
	echo $this->Html->link('Accept', array('controller'=>'Student','action'=>'acceptUserLesson', $lessonInvitation['UserLesson']['user_lesson_id'])), '<br /><br />';
	echo $this->Html->link('Cancel', array('controller'=>'Student','action'=>'cancelUserLesson', $lessonInvitation['UserLesson']['user_lesson_id']));
	echo '<br /><br /><br />';
}
*/?>

<h3>Subject Requests</h3>
--><?php
/*echo $this->Html->link('Make request', array('controller'=>'Requests','action'=>'makeRequest')),'<br />';
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
*/?>
