<?php echo $this->element('Panel/menu');  ?>

<h3>Datetime</h3>
<?php
echo $this->Form->create('UserLesson', array('type' => 'file'));
echo $this->Form->hidden('user_lesson_id');
echo $this->Form->input('datetime', array('type'=>'datetime'));
echo $this->Form->input('duration_minutes');
?>
<br />
<h3>Pricing</h3>
<?php
echo $this->Form->input('1_on_1_price');
echo $this->Form->input('max_students');
echo $this->Form->input('full_group_student_price');
if(isSet($groupPrice)) {
    echo 'Full Group Price: ',$groupPrice,'<br />';
}
echo $this->Form->submit('Save');
echo $this->Form->end();
?>