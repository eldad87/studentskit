<script type="text/javascript">
    $(document).ready(function() {
    <?php
        if(isSet($success)) {
            echo '$(\'#certificate-popup\').modal(\'hide\');';
        }

        if(isSet($updateExisting)) {
            //$certificateData
            echo 'var cert = \''.preg_replace('/\s\s+/', ' ', $this->element('Panel/Profile/certificate', array('cert'=>$certificateData))).'\';';
            echo '$(\''.$updateExisting.'\').html(cert);';
        }
        if(isSet($updateNew)) {
            //$certificateData
            echo 'var cert = \''.preg_replace('/\s\s+/', ' ', $this->element('Panel/Profile/certificate', array('cert'=>$certificateData, 'li'=>true))).'\';';
            echo '$(\''.$updateNew.'\').append(cert);';
        }
    ?>
        initCertificateJS();
        initCancelJS('.confirm-remove-certificate');
    });
</script>
<?php
$this->Form->create('TeacherCertificate', array('type' => 'file'));
    echo '<fieldset>';
        echo $this->Form->input('name');
        echo $this->Form->input('description');
        echo $this->Form->input('imageUpload', array('type' => 'file'));
        echo $this->Form->input('date');
    echo '</fieldset>';
$this->Form->end();
?>