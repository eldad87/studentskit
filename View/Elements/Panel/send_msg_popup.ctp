<?php
/**
 * Provide $linkSelector
 *  each link need to have data-entity_type, data-entity_id, data-to_user_id
 */
?>
<script type="text/javascript">
    $(document).ready(function() {

        //Unbind existing events
        //$('<?php echo $buttonSelector; ?>').unbind();
        //$('#msg-form').unbind();


        $('body').undelegate('<?php echo $buttonSelector; ?>', 'click');
        $('body').delegate('<?php echo $buttonSelector; ?>', 'click', function(e) {
        //$('<?php echo $buttonSelector; ?>').click(function(e){
            e.preventDefault();

            //Reset form's data-*
            resetData('#msg-form');

            //Copy data-* and place it as hidden:input
            $('#msg-form').data($(this).data());

            $('#msg-popup').modal('show');
        });

        pAPIObj.loadElement('#msg-form', 'submit', '#msg-popup .modal-body', 'post');
        pAPIObj.setAppendCallback('#msg-form', 'after', function(data){
            if(data['response']['title'][0]=='Success') {
                //Close popup
                $('#msg-popup').modal('hide');
            }
        });
    });
</script>
<div id="msg-popup" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
        <h3><?php echo __('Send a message'); ?></h3>
    </div>
    <?php echo $this->Form->create('User', array('class'=>'sk-form', 'url'=>array('controller'=>'Message', 'action'=>'sendMessage'),
    'method'=>'post', 'id'=>'msg-form')); ?>
    <div class="modal-body">
        <div class="fullwidth pull-left">
            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="send-message"><?php echo __('Message'); ?> :</label>
                    <div class="control control1">
                        <textarea class="x-large2" cols="30" rows="6" name="message" id="send-message"></textarea>
                    </div>
                </div>
            </fieldset>
        </div>
    </div> <!-- /modal-body -->

    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Cancel'); ?></button>
        <button class="btn btn-primary" id="send-msg"><?php echo __('Send'); ?></button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>