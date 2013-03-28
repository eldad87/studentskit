<script type="text/javascript">
    $(document).ready(function() {
    <?php
        if(isSet($success)) {
            echo '$(\'#certificate-popup\').modal(\'hide\');';
        }

        if(isSet($updateExisting)) {
            //$certificateData

            //echo 'var cert = \''.preg_replace('/\s\s+/', ' ', $this->element('Panel'.DS.'Profile'.DS.'certificate', array('cert'=>$certificateData))).'\';';
            echo 'var cert = \''.$this->Layout->stringToJSVar($this->element('Panel'.DS.'Profile'.DS.'certificate', array('cert'=>$certificateData))).'\';';
            echo '$(\''.$updateExisting.'\').html(cert);';
        }
        if(isSet($updateNew)) {
            //$certificateData
            //echo 'var cert = \''.preg_replace('/\s\s+/', ' ', $this->element('Panel'.DS.'Profile'.DS.'certificate', array('cert'=>$certificateData, 'li'=>true))).'\';';
            echo 'var cert = \''.$this->Layout->stringToJSVar($this->element('Panel'.DS.'Profile'.DS.'certificate', array('cert'=>$certificateData, 'li'=>true))).'\';';
            echo '$(\''.$updateNew.'\').append(cert);';
        }
    ?>
        initCertificateJS();
        initCancelJS('.confirm-remove-certificate', 1);
    });
</script>
<?php
$this->Form->create('TeacherCertificate', array('type' => 'file'));
    echo '<fieldset>';
        echo $this->Form->input('name', $this->Layout->styleForInput());
        echo $this->Form->input('description', $this->Layout->styleForInput());
        echo $this->Form->input('image_source', $this->Layout->styleForInput(array('type'=>'file', 'label'=>array('class'=>'control-label', 'text'=>__('Image')))));
        echo $this->Form->input('datetime', $this->Layout->styleForInput(array('type'=>'datetime', 'class'=>false)));
    echo '</fieldset>';
$this->Form->end();
?>