<?php

echo $this->Form->create('Image', array('type' => 'file'));
echo $this->Form->input('image', array('type' => 'file'));
echo $this->Form->end('Upload');