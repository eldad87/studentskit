<?php
/*echo $this->Facebook->logout(array( 'label'=>'logout', 'redirect'=>array('controller'=>'/', 'action'=>'logout')));
echo $this->element('SignMeUp.login');*/
?>
<div class="center-container">
    <?php echo $this->Form->create(null, array('class'=>'sk-form centered', 'url'=>'/login', 'method'=>'post', 'id'=>'login-form-html')); ?>

    <p class="textalign-left"><?php echo __('Don\'t have an account? click <a href="#register-popup" role="button"  data-dismiss="modal"  data-toggle="modal" id="toggle-to-registration-form">here</a> to register'); ?></p><br /><br />

    <p class="textalign-left"><?php echo $this->Facebook->login(array('perms'=>'email', 'label'=>'FB login', 'redirect'=>array('controller'=>'/', 'action'=>'login', '?'=>array('login_client'=>'facebook')))); ?></p>
    <fieldset>

        <?php echo $this->Form->input('email', $this->Layout->styleForInput()); ?>
        <?php echo $this->Form->input('password', $this->Layout->styleForInput(array('type'=>'password'))); ?>



        <div class="control-group ord-idnt textalign-right">
            <div class="control control1">
                <button class="btn" type="submit">Login</button>
            </div>
        </div>
    </fieldset>

    <?php echo $this->Form->end(); ?>

</div>