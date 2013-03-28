<?php

//Message was validated, and sent
if($post && isSet($sent)) {

    if($sent) {
        $message = __('Message sent, Thanks!');
    } else {
        $message = __('Sorry, message not sent. try again later.');
    }
?>

<div class="cont-span6 cbox-space">
    <h2><strong><?php echo $message; ?></strong></h2>
</div>

<?php
//Message was not sent
} else {

    if(!$post && !$ajax) {
        $this->extend('/Support/common/common');
        $this->start('main');
    }
?>

    <?php

    $form = $this->Form->create('Contact', array('class'=>'sk-form',
        'method'=>'post', 'id'=>'contact-form',
        'novalidate'=>'novalidate'));

    if(!$ajax) {
    ?>
        <script type="text/javascript">
            //Student profile page
            $(document).ready(function() {
                pfObj.loadForm('#contact-form', '#contact-area', 'post');

            });
        </script>
    <?php


        if(!$post) {
    ?>
        <div class="cont-span6 cbox-space">
            <h2><strong><?php echo __('Send us a message'); ?></strong></h2>
            <div class="fullwidth pull-left space7 space17">
                <div class="form-first" id="contact-area">
    <?php
        }


        //!ajax
        echo $form;
    }
    ?>

                <fieldset>
                    <?php
                    if($topic) {
                        echo $this->Form->hidden('topic', array('value'=>$topic));
                    } else {
                        echo $this->Form->input('topic', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2'),
                            'options'=>$topics)));
                    }?>

                    <?php echo $this->Form->input('subject', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2')))); ?>
                    <?php echo $this->Form->input('full_name', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2')))); ?>
                    <?php echo $this->Form->input('email', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2')))); ?>
                    <?php echo $this->Form->input('phone', $this->Layout->styleForInput(array('div'=>array('class'=>'control-group control2')))); ?>
                    <?php echo $this->Form->input('message', $this->Layout->styleForInput(array('type'=>'textarea','div'=>array('class'=>'control-group control2')))); ?>


                <?php
                if(!$ajax) {
                ?>
                    <div class="control-group control2">
                        <label class="control-label"></label>
                        <div class="control">
                            <button class="btn-blue pull-right" type="submit"><?php echo __('Send'); ?></button>
                        </div>
                    </div>
                <?php
                }
                ?>
                </fieldset>


    <?php

    $endForm = $this->Form->end();
    if(!$ajax) {
        echo $endForm;


        if(!$post) {
            ?>
                </div>
            </div>
        </div>
        <?php
            $this->end();
        }
    }
}
?>

