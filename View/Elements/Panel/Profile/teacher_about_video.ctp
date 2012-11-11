<?php
    if(isSet($li)) {
        echo '<li id="teacher_about_video_'.$video['teacher_about_video_id'].'">';
    }
?>
                <div class="certificate-box">
                    <div class="certificateicon-bar">
                        <a href="#" class="pull-left add-edit-teacher-about-video" data-update-existing-id="#teacher_about_video_<?php echo $video['teacher_about_video_id']; ?>" data-teacher_about_video_id="<?php echo $video['teacher_about_video_id']; ?>"><i class="iconMedium-add-pencil"></i></a>
                        <a href="#" class="pull-left confirm-remove-teacher-about-video" data-cancel-prefix="teacher_about_video" data-id="<?php echo $video['teacher_about_video_id']; ?>"><i class="iconMedium-add-del"></i></a>
                    </div>

                    <?php echo str_replace('/\\', '/', $this->Html->image($this->Layout->image(null, 80, 80), array('alt' => 'About video'))); ?>
                </div>
                <div class="certificate-text">
                    <h5><?php
                        App::uses('Languages', 'Utils.Lib');
                        $lang = new Languages();
                        $catLang = $lang->catalog($video['language']);
                        echo $catLang['language'] ?></h5>
                </div>
<?php
if(isSet($li)) {
    echo '</li>';
}
?>