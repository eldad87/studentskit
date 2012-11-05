<?php //echo $this->element('SignMeUp.register'); ?>

<div class="center-container">
    <?php echo $this->Form->create(null, array('class'=>'sk-form centered', 'url'=>'/register', 'method'=>'post', 'id'=>'register-form-html')); ?>

    <p class="textalign-left"><?php echo __('Don\'t have an account? click <a href="#register-popup" role="button"  data-dismiss="modal"  data-toggle="modal" id="toggle-to-registration-form">here</a> to register'); ?></p><br /><br />

    <fieldset>

        <?php echo $this->Form->input('first_name', $this->Layout->styleForInput()); ?>
        <?php echo $this->Form->input('last_name', $this->Layout->styleForInput()); ?>
        <?php echo $this->Form->input('email', $this->Layout->styleForInput()); ?>
        <?php echo $this->Form->input('password', $this->Layout->styleForInput(array('type'=>'password'))); ?>
        <?php echo $this->Form->input('password2', $this->Layout->styleForInput(array('label'=>array('text'=>__('Confirm Password'), 'class'=>'control-label'), 'type'=>'password'))); ?>
        <?php echo $this->Form->input('imageUpload', $this->Layout->styleForInput(array('type'=>'file'))); ?>



        <div class="control-group ord-idnt textalign-right">
            <div class="control control1">
                <button class="btn" type="submit">Register</button>
            </div>
        </div>
    </fieldset>

    <?php echo $this->Form->end(); ?>

</div>