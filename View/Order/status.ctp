<?php
$this->extend('/Order/Common/common');

$this->start('main');

if(isSet($approved)) {
    echo '<h2 class="pull-left fullwidth space14"><strong>'.__('Success').'</strong></h2>';

    echo '<p class="fontsize1 space13">'.__(sprintf('Your request for %s has been approved', $name)).'</p>';

    if($orderData['lesson_type']==LESSON_TYPE_LIVE) {
        echo '<p class="fontsize1 space13">'.__('Before the lesson starts, you\'ll get an email and notifications. Meanwhile, You can track the lesson status in your panel').
              $this->Html->link('here (under Upcoming)', array('controller'=>'Student', 'action'=>'lessons')).'</p>';
    } else {
        echo '<p class="fontsize1 space13">'.__('To watch the video, click: ').
            $this->Html->link(' here', array('controller'=>'Lessons', 'action'=>'video', $subjectId)).'</p>';
    }



} else if(isSet($pending_teacher_approval)) {
    echo '<h2 class="pull-left fullwidth space14"><strong>'.__('Success').'</strong></h2>';

    echo '<p class="fontsize1 space13">'.__('You\'re request is waiting for the teacher\'s approval. to check its status, click ').
        $this->Html->link('here', $this->Layout->getOrganizerUrl('/Student/lessons', '/Student/lessonsBooking')).'</p>';

} else if(isSet($pending_user_approval)) {
    die('Error');

} else {
    echo '<h2 class="pull-left fullwidth space14"><strong>'.__('Error').'</strong></h2>';
    echo '<p class="fontsize1 space13">'.__('Please contact support').'</p>';
}
?>
<?php
$this->end();
?>