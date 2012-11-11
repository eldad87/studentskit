<?php
/*echo $this->Html->link('Add About Video', array('controller'=>'Teacher', 'action'=>'addAboutVideo'));
if(!empty($userData['TeacherAboutVideo'])) {
    foreach($userData['TeacherAboutVideo'] AS $av) {
        pr($av);
        echo $this->Html->link('Remove About Video', array('controller'=>'Teacher', 'action'=>'removeAboutVideo', $av['teacher_about_video_id']));
    }
}
*/

echo $this->element('panel/cancel_popup', array('buttonSelector'=>'.confirm-remove-certificate',
                                                'appendId'=>1,
                                                'title'=>__('Remove your certificate'),
                                                'description'=>__('Do you want to proceed?'),
                                                'cancelUrl'=>array('controller'=>'Teacher', 'action'=>'removeCertificate', '{id}')));


echo $this->element('panel/certificate_popup', array('buttonSelector'=>'.add-edit-certificate'));

echo $this->element('panel/cancel_popup', array('buttonSelector'=>'.confirm-remove-teacher-about-video',
                                                                    'appendId'=>2,
                                                                    'title'=>__('Remove your about video'),
                                                                    'description'=>__('Do you want to proceed?'),
                                                                    'cancelUrl'=>array('controller'=>'Teacher', 'action'=>'removeAboutVideo', '{id}')));

echo $this->element('panel/teacher_about_video_popup', array('buttonSelector'=>'.add-edit-teacher-about-video'));
?>

<script type="text/javascript">
    //Student profile page
    $(document).ready(function(){
        pfObj.loadForm('#teacher-profile-form', '#main-area', 'post');

    });
</script>
<div class="cont-span6 cbox-space">
    <h2><strong><?php echo __('Profile'); ?></strong></h2>
    <div class="fullwidth pull-left space7 space17">
        <div class="form-first">
            <?php echo $this->Form->create('User', array('class'=>'sk-form', 'url'=>array('controller'=>'Teacher', 'action'=>'profile'),
                                                                                            'method'=>'post', 'id'=>'teacher-profile-form')); ?>
            <fieldset>
                <?php echo $this->Form->input('teacher_about', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2')))); ?>
                <?php echo $this->Form->input('teacher_address', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2')))); ?>
                <?php echo $this->Form->input('teacher_zipcode', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2')))); ?>
                <div class="control-group control2">
                    <label class="control-label"></label>
                    <div class="control">
                        <button class="btn-blue pull-right" type="submit">Save</button>
                    </div>
                </div>
            </fieldset>
            <?php echo $this->Form->end(); ?>
        </div>


        <div class="fullwidth pull-left space6">
            <h5 class="fullwidth pull-left space8"><?php echo __('Certificates'); ?></h5>

            <ul class="certificate-container" id="certificate_container">
                <?php
                foreach($userData['TeacherCertificate'] AS $cert) {
                    echo $this->element('Panel/Profile/certificate', array('cert'=>$cert, 'li'=>true));

                    //echo $this->Html->link('Remove Certificate', array('controller'=>'Teacher', 'action'=>'removeCertificate', $cert['teacher_certificate_id']));
                }
                ?>
            </ul>
            <button class="btn-blue pull-right add-edit-certificate" data-update-new-certificate-id="#certificate_container" type="Save">Add</button>
        </div>


        <div class="fullwidth pull-left space6">
            <h5 class="fullwidth pull-left space8"><?php echo __('About video'); ?></h5>

            <ul class="certificate-container" id="about_video_container">
                <?php
                foreach($userData['TeacherAboutVideo'] AS $av) {
                    echo $this->element('Panel/Profile/teacher_about_video', array('video'=>$av, 'li'=>true));
                    /*echo $this->Html->link('Remove About Video', array('controller'=>'Teacher', 'action'=>'removeAboutVideo', $av['teacher_about_video_id']));*/
                }
                ?>
            </ul>
            <button class="btn-blue pull-right add-edit-teacher-about-video" data-update-new-id="#about_video_container" type="Save">Add</button>
        </div>

    </div> <!-- /cpull-left space7 -->
</div> <!-- /cont-span6 -->
