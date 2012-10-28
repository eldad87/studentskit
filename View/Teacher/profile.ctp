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
<h3>About videos</h3>
<?php
echo $this->Html->link('Add About Video', array('controller'=>'Teacher', 'action'=>'addAboutVideo'));
if(!empty($userData['TeacherAboutVideo'])) {
    foreach($userData['TeacherAboutVideo'] AS $av) {
        pr($av);
        echo $this->Html->link('Remove About Video', array('controller'=>'Teacher', 'action'=>'removeAboutVideo', $av['teacher_about_video_id']));
    }
}
?>

<h3>Certificate</h3>
<?php
echo $this->Html->link('Add Certificate', array('controller'=>'Teacher', 'action'=>'addCertificate'));
if(!empty($userData['TeacherCertificate'])) {
    foreach($userData['TeacherCertificate'] AS $cert) {
        pr($cert);
        echo $this->Html->link('Remove Certificate', array('controller'=>'Teacher', 'action'=>'removeCertificate', $cert['teacher_certificate_id']));
    }
}
?>