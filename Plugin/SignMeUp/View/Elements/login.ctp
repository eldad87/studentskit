<?php

echo $this->Form->create();
echo $this->Form->input('email');
echo $this->Form->input('password', array('label' => 'Password', 'type' => 'password'));
echo $this->Form->end('Login');

?>