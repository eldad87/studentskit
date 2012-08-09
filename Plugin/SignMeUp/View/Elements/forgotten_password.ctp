<h2><?php echo __d('SignMeUp', 'Reset Your Password'); ?></h2>
<p><?php echo __d('SignMeUp', 'Please enter your email address below:'); ?></p>
<?php
echo $this->Form->create();
echo $this->Form->input('email');
echo $this->Form->end('Reset Password');
?>