<?php
$this->Html->scriptBlock('var jsSettings = jQuery.parseJSON(\''.json_encode($jsSettings).'\');', array('inline'=>false));
?>