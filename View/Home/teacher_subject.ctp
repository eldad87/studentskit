<?php

$this->extend('/Home/Common/subject');

$this->start('popups');
    if($settings['popup']) {

        if($user) {
            $settings['popup']['user'] = $user;
        }
        echo $this->element('Home\popup', $settings['popup']);
    }
$this->end();


$this->start('lesson_box');
    echo '<div class="black-cent-butn butns-width"><strong>',$settings['order_text'],'</strong></div>';

    $params = array('escape'=>false, 'class'=>'greencentbutn pull-left radius3');
    if($settings['popup']) {
        $params['data-toggle'] = 'modal';
    }
    echo $this->Html->link('<i class="iconBig-kart-icon pull-left"></i><span class="pull-left"><strong>'.$settings['order_button_text'].'</strong></span>',
        $settings['popup'] ? '#order-notice-popup' : $settings['order_url'],
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