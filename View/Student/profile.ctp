<script type="text/javascript">
    //Student profile page
    $(document).ready(function(){
        pfObj.loadForm('#user-profile-form', '#main-area', 'post');

        pfAPIObj.loadForm('#change-password-form', '#change-password-form .modal-body', 'post');
        pfAPIObj.setAppendCallback('#change-password-form', 'after', function(data){
            if(data['response']['title'][0]=='Success') {
                //Close popup
                $('#change-password-popup').modal('hide');
            }
        });


    });
</script>
<div class="cont-span6 cbox-space">
    <h2><strong><?php echo __('Profile'); ?></strong></h2>
    <div class="fullwidth pull-left space7">
        <?php echo $this->Form->create('User', array('class'=>'sk-form', 'url'=>array('controller'=>'Student', 'action'=>'profile'),
                                                        'method'=>'post', 'id'=>'user-profile-form', 'type' => 'file')); ?>
            <fieldset>
                <?php echo $this->Form->input('first_name', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2')))); ?>
                <?php echo $this->Form->input('last_name', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2')))); ?>
                <div class="control-group control2">
                    <label  class="control-label">Email :</label>
                    <div class="control">
                        <p><?php $user = $this->getVar('user'); echo $user['email']; ?></p>
                    </div>
                </div>
                <?php echo $this->Form->input('phone', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2')))); ?>
                <?php echo $this->Form->input('student_about', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2')))); ?>
                <?php echo $this->Form->input('last_name', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2')  ))); ?>
                <?php echo $this->Form->input('imageUpload', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2'), 'type' => 'file'  ))); ?>

                <div class="control-group control2">
                    <label class="control-label"></label>
                    <div class="control">
                        <button class="btn-blue pull-right" type="Save">Save</button>
                    </div>
                </div>
                <div class="control-group control2">
                    <label  class="control-label"><?php echo __('Password');?> :</label>
                    <div class="control space23">
                        <p><a data-toggle="modal" href="#change-password-popup"><?php echo __('Click here to change password');?></a></p>
                    </div>
                </div>
            </fieldset>
        <?php echo $this->Form->end(); ?>
    </div> <!-- /cpull-left space7 -->
</div> <!-- /cont-span6 -->
<!--<div class="cont-span3 pull-right c-box-mar2 banner-visibility">
    <div class="banner-box"></div>
</div>--><!-- /cont-span3 -->


<div id="change-password-popup" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3><?php echo __('Change password'); ?></h3>
    </div> <!-- /modal-header -->

    <?php echo $this->Form->create('User', array('class'=>'sk-form', 'url'=>array('controller'=>'Accounts', 'action'=>'changePassword'),
                                                'method'=>'post', 'id'=>'change-password-form')); ?>

    <div class="modal-body">
        <?php echo $this->Form->input('current_password',   $this->Layout->styleForInput(array('type'=>'password', 'class'=>'max-large', 'label' => array('class'=>'control-label', 'text'=>__('Current Password: '))))); ?>
        <?php echo $this->Form->input('password',       $this->Layout->styleForInput(array('type'=>'password', 'class'=>'max-large', 'label' => array('class'=>'control-label', 'text'=>__('New Password: '))))); ?>
        <?php echo $this->Form->input('password2',    $this->Layout->styleForInput(array('type'=>'password', 'class'=>'max-large', 'label' => array('class'=>'control-label', 'text'=>__('Repeat Password: '))))); ?>
    </div> <!-- /modal-body -->

    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Close'); ?></button>
        <button class="btn btn-primary"><?php echo __('Save'); ?></button>
    </div><!-- /modal-footer -->

    <?php echo $this->Form->end(); ?>

</div>