<?php

$this->extend('/Home/Common/subject');

$this->start('popups');
    if($settings['popup']) {

        if($user) {
            $settings['popup']['user'] = $user;
        }
        echo $this->element('Home'.DS.'popup', $settings['popup']);
    }
$this->end();


$this->start('lesson_box');
    echo '<div class="black-cent-butn butns-width"><strong>',$settings['order_text'],'</strong></div>';

    //btn btn-success pull-right orderButton space5
    $params = array('escape'=>false, 'class'=>'greencentbutn pull-left radius3 space5', 'style'=>'top:-5px;');

    $url = '';
    if($settings['popup']) {
        $params['data-toggle'] = 'modal';
        $url = '#order-notice-popup';
    } else {
        $url = $settings['order_url'];
        $params['class'] .= ' order-button';
        $params['escape'] = false;
        $params['data-statistics'] = $this->Layout->subjectStatistics($subjectData, (isSet($teacherLessonData) ? $teacherLessonData : array()));
    }
    //echo $this->Html->link('<div><i class="iconBig-kart-icon pull-left"></i></div><span class="pull-left"><strong>'.$settings['order_button_text'].'</strong></span>',
    echo $this->Html->link('<i class="iconBig-kart-icon pull-left"></i><span class="pull-left"><strong>'.$settings['order_button_text'].'</strong></span>',
        $url,
        $params
    );
$this->end();


$this->start('topic_image');
    if($subjectData['video_source']) {
        echo $this->Layout->videoPlayer($subjectData['video_source'], $this->Layout->image($subjectData['image_source'], 436, 214), 436, 214);
    } else {
        echo $this->Html->image($this->Layout->image($subjectData['image_source'], 436, 214), array('alt' => 'Topic image'));
    }
$this->end();