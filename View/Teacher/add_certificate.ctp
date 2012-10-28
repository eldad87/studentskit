<h3>TeacherCertificate</h3>
<?php 
echo $this->Form->create('TeacherCertificate', array('type' => 'file'));
echo $this->Form->input('name');
echo $this->Form->input('description');
echo $this->Form->input('imageUpload', array('type' => 'file'));
echo $this->Form->input('date');
echo $this->Form->submit('Save');
echo $this->Form->end();
?>