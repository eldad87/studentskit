<?php
/**
 * Provide $linkSelector
 *  each link need to have data-id
 *
 * $cancelUrl - the url of the cancel API request: array('controller'=>'Student', 'action'=>'cancelUserLesson', '{id}')
 */
?>
<script type="text/javascript">
    $(document).ready(function() {


        $('<?php echo $buttonSelector; ?>').click(function(e){
            e.preventDefault();

            //Copy data-* and place it as hidden:input
            $('#cancel-upcoming-approved').data($(this).data());

            //Show popup
            $('#cancel-upcoming-popup').modal('show');
        });

        pAPIObj.loadElement('#cancel-upcoming-approved', 'click', '#cancel-upcoming-popup .modal-body', 'post');
        pAPIObj.setAppendCallback('#cancel-upcoming-approved', 'after', function(data){
            if(data['response']['title'][0]=='Success') {
                //Close popup
                $('#cancel-upcoming-popup').modal('hide');


                var divId = $('#cancel-upcoming-approved').data('cancel-prefix') + '_' + $('#cancel-upcoming-approved').data('id');
                //Remove lesson box
                $( '#' + divId ).hide();
            }
        });
    });
</script>
<div id="cancel-upcoming-popup" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
        <h3><?php echo __('Cancel lesson participation'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __('You are about to cancel your participation, this procedure may be irreversible.'); ?></p>
        <p><?php echo __('Do you want to proceed?'); ?></p>
    </div>
    <div class="modal-footer">
        <a href="#" id="cancel-upcoming-approved" data-target="<?php echo Router::url($cancelUrl); ?>" class="btn danger"><?php echo __('Yes'); ?></a>
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('No'); ?></button>
    </div>
</div>