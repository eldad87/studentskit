<?php
$inline = isSet($inline) ? $inline : false;
echo $this->Html->scriptBlock('if(jsSettings===undefined) { jsSettings={}; }  var jsSettings = $.extend( jsSettings, jQuery.parseJSON(\''.json_encode($jsSettings).'\') ); ', array('inline'=>$inline));
?>