<?php
$statisticsJSON = json_encode($statistics);

$this->Html->scriptBlock('
$(document).ready(function() {
    trackData = jQuery.parseJSON(\''.$statisticsJSON.'\'); //Not var trackData - so it will be global and available in the calendar.js

    mixpanel.track("Order. calendar load", trackData);

    $(\'#orderNextButton\').click(function() {
        mixpanel.track("Order. Calendar next click", trackData);

        showError(\'#calendar-msg\', \''.__('Error').'\' ,\''.__('Please select a date.').'\');
    });

     $(\'.upcoming-lesson-join\').click(function() {
            mixpanel.track("Order. Calendar upcoming lesson join click", trackData);
    });
     $(\'.upcoming-lesson-open\').click(function() {
        mixpanel.track("Order. Calendar upcoming lesson open click", trackData);
    });

});
', array('inline'=>false));


$this->set('nextOrderStep', true);
$this->extend('/Order/Common/common');

$this->start('main');
echo $this->element('Order'.DS.'calendar', array('allLiveLessons'=>$allLiveLessons));
$this->end();
?>