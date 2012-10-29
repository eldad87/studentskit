<?php
$this->extend('/Order/Common/common');

$this->start('main');
echo $this->element('Order/calendar', array('monthLessons'=>$allLiveLessons['records'], 'month'=>$allLiveLessons['month'], 'year'=>$allLiveLessons['year']));

echo $this->Form->create('UserLesson', array('url'=>array('controller'=>'Order', 'action'=>'setLessonDatetime'), 'type'=>'post'));
echo $this->Form->input('datetime', array('type'=>'datetime'));
echo $this->Form->end('order');

$this->end();
?>