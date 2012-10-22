<?php echo $this->Facebook->login(array('perms'=>'email', 'label'=>'FB login', 'redirect'=>array('controller'=>'/', 'action'=>'login', '?'=>array('login_client'=>'facebook')))); ?><br />
<?php //echo $this->Facebook->logout(array( 'label'=>'logout', 'redirect'=>array('controller'=>'/', 'action'=>'logout'))); ?>

<?php echo $this->element('SignMeUp.login'); ?>