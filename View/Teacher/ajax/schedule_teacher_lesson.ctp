<script type="text/javascript">
<?php
if(isSet($success)) {
    echo '$(\'#schedule-popup\').modal(\'hide\');';
} else if(isSet($error)) {
    echo 'showError(\'#schedule-popup .modal-body\',\''.__('Internal Error').'\', \'\')';
}
?>
</script>
<?php

$this->Form->create('TeacherLesson', array('type' => 'file'));

echo $this->Form->input('datetime', $this->Layout->styleForInput(array('type'=>'datetime', 'class'=>false)));
echo $this->Form->input('is_public', $this->Layout->styleForInput(array('options'=>array(SUBJECT_IS_PUBLIC_TRUE=>__('Yes'), SUBJECT_IS_PUBLIC_FALSE=>__('No')))));


?>

