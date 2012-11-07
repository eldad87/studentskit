<div id="login-popup" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="login-register" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3><?php echo __('Login'); ?></h3>
    </div> <!-- /modal-header -->

    <?php echo $this->Form->create('User', array('class'=>'sk-form', 'url'=>'/login', 'method'=>'post', 'id'=>'login-form')); ?>
    <div class="modal-body">
        <p><?php echo __('Don\'t have an account? click <a href="#register-popup" role="button"  data-dismiss="modal"  data-toggle="modal" id="toggle-to-registration-form">here</a> to register'); ?><br /><br /></p>

        <fieldset>

            <div class="control-group">
                <label class="control-label" for="login-email"><?php echo __('Email'); ?> :</label>
                <div class="control control1">
                    <?php echo $this->Form->input('email',  array('label' => false, 'class'=>'x-large2', 'id'=>'login-email', 'error'=>false)); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="login-password"><?php echo __('Password'); ?> :</label>
                <div class="control control1">
                    <?php echo $this->Form->input('password', array('label' => false, 'type' => 'password', 'class'=>'x-large2', 'id'=>'login-password', 'error'=>false)); ?>
                </div>
            </div>
        </fieldset>
        <br />
        <?php echo $this->Facebook->login(array('perms'=>'email', 'label'=>'FB login', 'redirect'=>array('controller'=>'/', 'action'=>'login', '?'=>array('login_client'=>'facebook')))); ?>
    </div> <!-- /modal-body -->

    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Close'); ?></button>
        <button class="btn btn-primary"><?php echo __('Login'); ?></button>
    </div><!-- /modal-footer -->

    <?php echo $this->Form->end(); ?>
</div>




<div id="register-popup" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="login-register" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3><?php echo __('Register'); ?></h3>
    </div> <!-- /modal-header -->

    <?php echo $this->Form->create('User', array('class'=>'sk-form', 'url'=>'/register', 'method'=>'post', 'id'=>'register-form', 'type' => 'file')); ?>
    <div class="modal-body">
        <p><?php echo __('Have an account? click <a href="#login-popup" role="button"  data-dismiss="modal"  data-toggle="modal" id="toggle-to-registration-form">here</a> to login'); ?><br /><br /></p>

        <fieldset>
            <div class="control-group">
                <label class="control-label" for="register-first_name"><?php echo __('First name'); ?>* :</label>
                <div class="control control1">
                    <?php echo $this->Form->input('first_name',  array('label' => false, 'class'=>'x-large2', 'id'=>'register-first_name', 'error'=>false)); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="register-last_name"><?php echo __('Last name'); ?> :</label>
                <div class="control control1">
                    <?php echo $this->Form->input('last_name',  array('label' => false, 'class'=>'x-large2', 'id'=>'register-last_name', 'error'=>false)); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="register-email"><?php echo __('Email'); ?>* :</label>
                <div class="control control1">
                    <?php echo $this->Form->input('email',  array('label' => false, 'class'=>'x-large2', 'id'=>'register-email', 'error'=>false)); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="register-password"><?php echo __('Password'); ?>* :</label>
                <div class="control control1">
                    <?php echo $this->Form->input('password',  array('label' => false, 'type' => 'password', 'class'=>'x-large2', 'id'=>'register-password', 'error'=>false)); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="register-password2"><?php echo __('Repeat Password'); ?>* :</label>
                <div class="control control1">
                    <?php echo $this->Form->input('password2',  array('label' => false, 'type' => 'password', 'class'=>'x-large2', 'id'=>'register-password2', 'error'=>false)); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="register-imageUpload"><?php echo __('Profile image'); ?> :</label>
                <div class="control control1">
                    <?php echo $this->Form->input('imageUpload',  array('label' => false, 'type' => 'file', 'class'=>'x-large2', 'id'=>'register-imageUpload', 'error'=>false)); ?>
                </div>
            </div>

        </fieldset>
    </div> <!-- /modal-body -->

    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Close'); ?></button>
        <button class="btn btn-primary"><?php echo __('Register'); ?></button>
    </div><!-- /modal-footer -->

    <?php echo $this->Form->end(); ?>
</div>