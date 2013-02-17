<?php echo $this->element('Panel'.DS.'menu');  ?>

<h3>Create teacher lesson</h3>
<?php 
echo $this->Form->create('TeacherLesson', array('type' => 'file'));
echo $this->Form->input('datetime', array('type'=>'datetime'));
echo $this->Form->input('is_public', array('options'=>array(1=>'on', 0=>'off')));


echo $this->Form->submit('Save');
echo $this->Form->end();
?>

