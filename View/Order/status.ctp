<?php
$this->extend('/Order/Common/common');

$this->start('main');
?>
<h2 class="pull-left fullwidth space14"><strong>Success</strong></h2>

<?php
if(isSet($approved)) {
    echo '<p class="fontsize1 space13">'.__(sprintf('Your request for %s has been approved', $name)).'</p>';

    if($orderData['lesson_type']==LESSON_TYPE_LIVE) {
        echo '<p class="fontsize1 space13">'.__('Before the lesson starts, you\'ll get an email and notifications. Meanwhile, You can track the lesson status in your panel').
              $this->Html->link('here (under Upcoming)', array('controller'=>'Student', 'action'=>'lessons')).'</p>';
    } else {
        echo '<p class="fontsize1 space13">'.__('To watch the video, click: ').
            $this->Html->link(' here', array('controller'=>'Lessons', 'action'=>'video', $subjectId)).'</p>';
    }



} else if(isSet($pending_teacher_approval)) {
    echo '<p class="fontsize1 space13">'.__('You\'re request is waiting for the teacher\'s approval. to check its status, click').
        $this->Html->link(' here (Under Booking)', array('controller'=>'Student', 'action'=>'lessons')).'</p>';

} else if(isSet($pending_user_approval)) {
    die('Error');
}
?>

<!--
<h2 class="pull-left fullwidth space14"><strong>Or</strong></h2>
<p class="fontsize1 space8">You‘rewaiting for teacher approval, meanwhile you can follow the status of you request in the
    <a href="#"><strong> “BOOKING“ </strong></a>tab in your panel.</p>
-->
<?php
$this->end();
?>