<script type="text/javascript">
    $(document).ready(function() {
    <?php
    if(isSet($success)) {
        echo '$(\'#teacher-about-video-popup\').modal(\'hide\');';
    }

    if(isSet($updateExisting)) {
        //$certificateData
        echo 'var cert = \''.preg_replace('/\s\s+/', ' ', $this->element('Panel/Profile/teacher_about_video', array('video'=>$aboutVideoData))).'\';';
        echo '$(\''.$updateExisting.'\').html(cert);';
    }
    if(isSet($updateNew)) {
        //$certificateData
        echo 'var cert = \''.preg_replace('/\s\s+/', ' ', $this->element('Panel/Profile/teacher_about_video', array('video'=>$aboutVideoData, 'li'=>true))).'\';';
        echo '$(\''.$updateNew.'\').append(cert);';
    }
    ?>
        initTeacherAboutVideoJS();
        initCancelJS('.confirm-remove-teacher-about-video', 2);
    });
</script>
<?php
$this->Form->create('TeacherAboutVideo', array('type' => 'file'));
    echo '<fieldset>';
        echo $this->Form->input('language', $this->Layout->styleForInput(array('options'=>$languages)));
        echo $this->Form->input('video_source', $this->Layout->styleForInput(array('type'=>'file', 'label'=>array('class'=>'control-label', 'text'=>__('About video')))));
    echo '</fieldset>';
$this->Form->end();
?>