<?php
echo 'Name: ',$name,'<br />';
echo 'description: ',$description,'<br />';
echo 'lesson_type: ',$lesson_type,'<br />';
echo 'datetime: ',$datetime,'<br />';
echo '1 on 1 price: ',$price,'<br />';

if(isSet($max_students)) {
    echo 'max_students: ',$max_students,'<br />';
}

if(isSet($num_of_students)) {
    echo 'num_of_students: ',$num_of_students,'<br />';
}
if(isSet($full_group_student_price)) {
    echo 'full_group_student_price: ',$full_group_student_price,'<br />';
}
if(isSet($full_group_total_price)) {
    echo 'full_group_total_price: ',$full_group_total_price,'<br />';
}

//TODO: POST

echo $this->Form->create(false, array('url'=>array('controller'=>'Order', 'action'=>'prerequisites'), 'type'=>'post'));
echo $this->Form->end('Continue');
?>