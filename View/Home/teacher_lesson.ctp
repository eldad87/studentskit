<?php
$this->extend('/Home/Common/subject');


$orderURL = array('controller'=>'Order', 'action'=>'init', 'join', $teacherLessonData['teacher_lesson_id']);
$orderButton = array();
$popup = array();
$playLink = array();


$this->start('lesson_box');
    echo '<div class="black-cent-butn butns-width"><strong>Join a LIVE lesson</strong></div>';

    if($showGoToLessonButton) {
        //No popup
        $popup = false;

        //No order button
        $orderButton = false;

        //onclick the play button - redirect to view the video
        $playLink = array('controller'=>'lessons', 'action'=>'video', $subjectData['subject_id']);

    } else {
        $playLink = false;

        if ($showPayForLessonButton || $showJoinForFreeLessonButton) {
            //Popup - order the lesson first
            $orderButton = $orderURL;
            $playLink = false;
            $popup = array('description'=>'Please order the lesson first',
                'button'=>array(array('name'=>'Order', 'url'=>$orderURL)));

        } else if($showPendingTeacherApproval) {
            //Popup - pending for the teacher approval
            $orderButton = false;
            $playLink = false;

            $popup = array('description'=>'You\'re last order is pending for the teacher approval',
                'button'=>array(array('name'=>'I want to order again', 'url'=>$orderURL)));

        } else  if($showAcceptInvitationButton) {
            //Popup - you have an invitation pending for you're approval
            $orderButton = false;
            $playLink = false;

            $popup = array('description'=>'You already have an invitation pending for you\'re approval',
                'button'=>array(array('name'=>'I want to order anyway', 'url'=>$orderURL)/*,
                                                                array('name'=>'View invitation', 'url'=>$orderURL)*/));
        }

        //Order button
        echo $this->Html->link('<i class="iconBig-kart-icon pull-left"></i><span class="pull-left"><strong>Order</strong></span>',
            ($orderButton ? $orderButton : '#myModal1'),
            array('escape'=>false, 'class'=>'greencentbutn pull-left radius3', 'data-toggle'=>'modal')
        );
    }

    if($popup) {

        if($user) {
            $popup['user'] = $user;
        }
        echo $this->element('Home\popup', $popup);
    }
$this->end();
$this->start('order_button');
$this->end();

/*
echo '<h3>Teacher Lesson</h3>';


echo 'showPendingTeacherApproval', var_dump($showPendingTeacherApproval),'<br />';
echo 'showAcceptInvitationButton', var_dump($showAcceptInvitationButton),'<br />';
echo 'showGoToLessonButton', var_dump($showGoToLessonButton),'<br />';
echo 'showPayForLessonButton', var_dump($showPayForLessonButton),'<br />';
echo 'showJoinForFreeLessonButton', var_dump($showJoinForFreeLessonButton),'<br />','<br />';

pr($teacherLessonData);*/
?>