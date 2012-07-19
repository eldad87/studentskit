<?php

echo $this->Form->create();
echo $this->Form->input('first_name');
echo $this->Form->input('last_name');
echo $this->Form->input('email');
echo $this->Form->input('password', array('label' => 'Password', 'type' => 'password'));
echo $this->Form->input('password2', array('label' => 'Confirm password', 'type' => 'password'));
echo $this->Form->end('Register');

?>