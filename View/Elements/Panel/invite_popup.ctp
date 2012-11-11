<?php
/**
 * Provide $linkSelector
 *  each link need to have teacher_lesson_id or subject_id
 */
?>
<script type="text/javascript">
    $(document).ready(function() {

        //Unbind existing events
        $(<?php echo $buttonSelector; ?>).unbind();
        $('#invitation-form').unbind();

        $('<?php echo $buttonSelector; ?>').click(function(e){
            e.preventDefault();

            //Reset form's data-*
            resetData('#invitation-form');

            //Copy data-* and place it as hidden:input
            $('#invite-form').data($(this).data());

            //Show popup
            $('#invitation-popup').modal('show');
        });

        pAPIObj.loadElement('#invite-form', 'submit', '#invitation-popup .modal-body', 'post');
        pAPIObj.setAppendCallback('#invite-form', 'after', function(data){
            if(data['response']['title'][0]=='Success') {
                //Close popup
                $('#invitation-popup').modal('hide');
            }
        });
    });
</script>
<div id="invitation-popup" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
        <h3><?php echo __('Invite users'); ?></h3>
    </div>
    <?php echo $this->Form->create('User', array('class'=>'sk-form', 'url'=>array('controller'=>'Lessons', 'action'=>'invite'),
                                                                                    'method'=>'post', 'id'=>'invite-form')); ?>
    <div class="modal-body">
        <div class="fullwidth pull-left">
            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="invite-emails"><?php echo __('Emails'); ?> :</label>
                    <div class="control control1">
                        <input name="emails" class="x-large2" type="text"  id="invite-emails" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="invite-message"><?php echo __('Message'); ?> :</label>
                    <div class="control control1">
                        <textarea class="x-large2" cols="30" rows="6" name="message" id="invite-message"></textarea>
                    </div>
                </div>
            </fieldset>
        </div>
    </div> <!-- /modal-body -->

    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('No'); ?></button>
        <button class="btn btn-primary" id="send-invitation"><?php echo __('Invite'); ?></button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>