<h3>TeacherCertificate</h3>
<?php 
echo $this->Form->create('TeacherAboutVideo', array('type' => 'file'));
echo '<br />';
echo $this->Form->input('language', array('options'=>$languages));
echo '<br />';
echo '<br />';
echo $this->Form->input('videoUpload', array('type' => 'file'));
echo '<br />';
echo $this->Form->submit('Save');
echo $this->Form->end();
?>