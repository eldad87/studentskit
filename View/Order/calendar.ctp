<?php
$this->Html->scriptBlock('
$(document).ready(function() {
    $(document).ready(function(){
        $(\'#orderNextButton\').click(function() {
            showError(\'#calendar-msg\', \''.__('Error').'\' ,\''.__('Please select a date.').'\');
        });
    });
});
', array('inline'=>false));


$this->set('nextOrderStep', true);
$this->extend('/Order/Common/common');

$this->start('main');
echo $this->element('Order/calendar', array('allLiveLessons'=>$allLiveLessons));
$this->end();
?>