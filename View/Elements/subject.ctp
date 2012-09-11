<div class="subject">
<?php
echo 'Subject id:'.$subject_id;
echo '<br />';
echo 'Name: '.$name;
echo '<br />';
echo 'Description: '.$description;
    echo '<br />';
echo 'language: '.$language;
echo '<br />';
echo 'Rating: '.$avarage_rating; 
echo '<br />';
echo '1 On 1 Price: '.$one_on_one_price;
echo '<br />';
echo 'Full Group Student price: '.$full_group_student_price;
echo '<br />';
echo 'Full Group Total price: '.$full_group_total_price;
echo '<br />';
echo 'Max students: '.$max_students;
echo '<br />';
echo $this->Html->link('Order', array('controller'=>'Order', 'action'=>'init', 'order', $subject_id));
echo '<br />';
echo $this->Html->link('Teacher page', array('controller'=>'Home', 'action'=>'teacher', (isSet($user_id) ? $user_id : $student_user_id)));
echo '<br />';
echo $this->Html->link('Teacher Subject page', array('controller'=>'Home', 'action'=>'teacherSubject', $subject_id));
?>
</div>