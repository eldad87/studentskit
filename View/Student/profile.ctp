<?php echo $this->element('Panel/menu');  ?>

<h3>Profile</h3>
<?php 
echo $this->Form->create('User', array('type' => 'file'));
echo $this->Form->hidden('user_id');
echo $this->Form->input('first_name');
echo $this->Form->input('last_name');
echo $this->Form->input('email');
echo $this->Form->input('phone');
echo $this->Form->input('student_about');
echo $this->Form->input('address');
echo $this->Form->input('zipcode');
echo $this->Form->input('imageUpload', array('type' => 'file'));
echo $this->Form->submit('Save');
echo $this->Form->end();
?>