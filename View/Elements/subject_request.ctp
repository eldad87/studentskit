<div class="subject">
<?php
echo 'Subject id:'.$Subject['subject_id'];
echo '<br />';
echo 'Name: '.$Subject['name'];
echo '<br />';
echo 'Description: '.$Subject['description'];
echo '<br />';
echo 'Language: '.$Subject['language'];
echo '<br />';
echo 'Lesson Type: '.$Subject['lesson_type']; 
echo '<br />';
echo 'Rating: '.$Subject['avarage_rating']; 
echo '<br />';
echo '1 On 1 Price: '.$Subject['1_on_1_price'];
echo '<br />';
echo 'Full Group Student price: '.$Subject['full_group_student_price'];
echo '<br />';
echo 'Full Group Total price: '.$Subject['full_group_total_price'];
echo '<br />';
echo 'Max students: '.$Subject['max_students'];
echo '<br />';
echo 'Student image: '.$Student['image'];
echo '<br />';
echo $this->Html->link('Offer', array('controller'=>'Requests', 'action'=>'offerLesson', $Subject['subject_id']));
?>
</div>