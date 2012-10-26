<?php
$this->extend('/Home/Common/subject');

$this->start('lesson_box');

    $orderURL   = array('controller'=>'Order', 'action'=>'init', 'order', $subjectData['subject_id']);
    $orderButton= array();
    $popup      = array();
    $playLink   = array();

    if(empty($settings)) {
        $settings = array(
            'order_text'=>'Order LIVE lesson',
            'order_button_text'=>'Order',
            'play_link' =>false,
            'popup'     =>array(),
            'order_url' =>array('controller'=>'Order', 'action'=>'init', 'order', $subjectData['subject_id'])
        );
    }
    //Live lesson
    if($subjectData['lesson_type']==LESSON_TYPE_LIVE) {
        echo '<div class="black-cent-butn butns-width"><strong>',$settings['order_text'],'</strong></div>';

        echo $this->Html->link('<i class="iconBig-kart-icon pull-left"></i><span class="pull-left"><strong>'.$settings['order_button_text'].'</strong></span>',
            $orderURL,
            array('escape'=>false, 'class'=>'greencentbutn pull-left radius3', 'data-toggle'=>'modal')
        );

        //No play link
        $playLink = $settings['play_link'];
        $popup = $settings['popup'];
        $orderURL = $settings['order_url'];


    //Video lesson
    } else {
        echo '<div class="black-cent-butn butns-width"><strong>Order VIDEO lesson</strong></div>';

        if($showGoToLessonButton) {
            //No popup
            $popup = false;

            //No order button
            $orderButton = false;

            //onclick the play button - redirect to view the video
            $playLink = array('controller'=>'lessons', 'action'=>'video', $subjectData['subject_id']);

        } else {
            $playLink = false;

            if ($showOrderLessonButton || $showOrderFreeLessonButton) {
                //Popup - order the lesson first
                $orderButton = $orderURL;
                //$playLink = false;
                $popup = array('description'=>'Please order the lesson first',
                    'button'=>array(array('name'=>'Order', 'url'=>$orderURL)));

            } else if($showPendingTeacherApproval) {
                //Popup - pending for the teacher approval
                $orderButton = false;
                //$playLink = false;

                $popup = array('description'=>'You\'re last order is pending for the teacher approval',
                    'button'=>array(array('name'=>'I want to order again', 'url'=>$orderURL)));

            } else  if($showAcceptInvitationButton) {
                //Popup - you have an invitation pending for you're approval
                $orderButton = false;
                //$playLink = false;

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
    }

    if($popup) {

        if($user) {
            $popup['user'] = $user;
        }
        echo $this->element('Home\popup', $popup);
    }
$this->end();


$this->start('order_button');

if($playLink===false) {
    echo $this->Html->image($this->Layout->image($subjectData['image_source'], 436, 214), array('alt' => 'Topic image'));
} else {
    echo $this->Html->link($this->Html->image($this->Layout->image($subjectData['image_source'], 436, 214), array('alt' => 'Topic image')),
        ($playLink ? $playLink : '#myModal1'),
        array('escape'=>false, 'data-toggle'=>'modal')
    );
}
$this->end();