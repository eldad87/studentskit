<?php
$this->Html->scriptBlock('
$(document).ready(function() {
    mixpanel.track("Order. calendar load");

    $(\'#orderNextButton\').click(function() {
        mixpanel.track("Order. Calendar next click");

        showError(\'#calendar-msg\', \''.__('Error').'\' ,\''.__('Please select a date.').'\');
    });

     $(\.upcoming-lesson-join\').click(function() {
            mixpanel.track("Order. Calendar upcoming lesson join click");
    });
     $(\.upcoming-lesson-open\').click(function() {
        mixpanel.track("Order. Calendar upcoming lesson open click");
    });

});
', array('inline'=>false));


$this->set('nextOrderStep', true);
$this->extend('/Order/Common/common');

$this->start('main');
echo $this->element('Order'.DS.'calendar', array('allLiveLessons'=>$allLiveLessons));
$this->end();
?>