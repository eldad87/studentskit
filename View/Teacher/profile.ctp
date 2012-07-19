<?php echo $this->element('Panel/menu');  ?>

<h3>Profile</h3>
<?php 
echo $this->Form->create('User', array('type' => 'file'));
echo $this->Form->hidden('user_id');
echo $this->Form->input('teacher_about');
echo $this->Form->input('teaching_address');
echo $this->Form->input('teacher_zipcode');
echo $this->Form->submit('Save');
echo $this->Form->end();
?>