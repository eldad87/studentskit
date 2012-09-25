<?php
echo '<h3>Teacher Subject</h3>';
echo '<br /><br /><h3>Owner user id</h3>';
echo $subjectData['user_id'],'<br />';
echo '<br /><br /><h3>Subject catalog id</h3>';
echo $subjectData['catalog_id'];

echo '<br /><br /><h3>',$lessonType,' Button</h3>';
if($lessonType=='live') {
    if($paymentNeeded) {
        echo 'Order lesson';
    } else {
        echo 'Order free lesson';
    }

} else if($lessonType=='video') {
    if($showPendingTeacherApproval) {
        echo 'Pending - disable button <br />';
        echo 'Popup: there is an existing request, you can negotiate/cancel it on your panel';

    } else if($showAcceptInvitationButton) {
        echo 'Pending - disable button <br />';
        echo 'Popup: there is an existing invitation, you can accept/decline it on your panel';

    } else if($showGoToLessonButton) {
        echo 'Go to lesson';

    } else if($showOrderLessonButton) {
        echo 'Order lesson';

    } else if($showOrderFreeLessonButton) {
        echo 'Order free lesson';
    }
}
echo '<br /><br />';


$subjectData['one_on_one_price'] = $subjectData['1_on_1_price'];
echo $this->element('subject', $subjectData);

echo '<br /><br /><h3>Subject Rating</h3>';
echo $this->element('total_rating', $subjectData);

echo '<br /><br /><h3>Profile</h3>';
unset($teacherUserData['teacher_total_teaching_minutes'], $teacherUserData['teacher_students_amount'], $teacherUserData['teacher_raters_amount']);
echo $this->element('profile', $teacherUserData);

if($subjectRatingByStudents) {
    echo '<br /><br /><h3>Subject Reviews</h3>';
    foreach($subjectRatingByStudents AS $subjectRatingByStudent) {
        echo $this->element('student_review', $subjectRatingByStudent['UserLesson']);
        echo '<br /> ';
    }
}

if($teacherOtherSubjects) {
    echo '<br /><br /><h3>Teacher other subjects</h3>';
    foreach($teacherOtherSubjects AS $teacherOtherSubject) {
        $teacherOtherSubject['Subject']['one_on_one_price'] = $teacherOtherSubject['Subject']['1_on_1_price'];
        echo $this->element('subject', $teacherOtherSubject['Subject']);
        echo '<br /> ';
    }
}

if(isSet($otherTeacherForThisSubject)) {
    echo '<br /><br /><h3>Other teachers on this subject</h3>';
    foreach($otherTeacherForThisSubject AS $subject) {
        $subject['Subject']['one_on_one_price'] = $subject['Subject']['1_on_1_price'];
        echo $this->element('subject', $subject['Subject']);
        echo '<br /> ';
    }
}


?>