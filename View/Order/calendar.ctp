<?php
$this->extend('/Order/Common/common');

$this->start('main');
echo $this->element('Order/calendar', array('allLiveLessons'=>$allLiveLessons));
$this->end();
?>