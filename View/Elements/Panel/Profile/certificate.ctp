<?php
    if(isSet($li)) {
        echo '<li id="certificate_'.$cert['teacher_certificate_id'].'">';
    }
?>
                <div class="certificate-box">
                    <div class="certificateicon-bar">
                        <a href="#" class="pull-left add-edit-certificate" data-update-existing-certificate-id="#certificate_<?php echo $cert['teacher_certificate_id']; ?>" data-teacher_certificate_id="<?php echo $cert['teacher_certificate_id']; ?>"><i class="iconMedium-add-pencil"></i></a>
                        <a href="#" class="pull-left confirm-remove-certificate" data-cancel-prefix="certificate" data-id="<?php echo $cert['teacher_certificate_id']; ?>"><i class="iconMedium-add-del"></i></a>
                    </div>

                    <?php echo str_replace('/\\', '/', $this->Html->image($this->Layout->image($cert['image_source'], 80, 80), array('alt' => 'Certificate'))); ?>
                </div>
                <div class="certificate-text">
                    <h5><?php echo $cert['name']; ?></h5>
                    <p><?php echo $cert['description']; ?></p>
                </div>
<?php
if(isSet($li)) {
    echo '</li>';
}
?>