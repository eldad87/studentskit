<?php
/**
 * Provide $linkSelector
 *  each link need to have data-id
 *  cancel-prefix - will remove the element with data-cancel-prefix + data-id
 *
 * $cancelUrl - the url of the cancel API request: array('controller'=>'Student', 'action'=>'cancelUserLesson', '{id}')
 */
if(!isSet($appendId)) {
    $appendId = 1;
}
$popupId = 'cancel-popup_'.$appendId;
$buttonId = 'cancel-approved_'.$appendId;

?>
<script type="text/javascript">

    function initCancelJS(buttonSelector, i) {

        buttonId =  'cancel-approved_' + i;
        popupId =  'cancel-popup_' + i;

        //Unbind existing events
        $(buttonSelector).unbind();
        $('#'+buttonId).unbind();

        $(buttonSelector).click(function(e){
            e.preventDefault();

            //resetData('#'+buttonId);


            //Copy data-*
            $('#'+buttonId).data($(this).data());

            //Show popup
            $('#' + popupId).modal('show');
        });

        pAPIObj.loadElement('#'+buttonId, 'click', '#' + popupId + ' .modal-body', 'post');
        pAPIObj.setAppendCallback('#'+buttonId, 'after', function(data){
            if(data['response']['title'][0]=='Success') {
                //Close popup
                $('#' + popupId).modal('hide');


                if($('#'+buttonId).data('cancel-prefix')) {
                    var divId = $('#'+buttonId).data('cancel-prefix') + '_' + $('#'+buttonId).data('id');
                    //Remove lesson box
                    $( '#' + divId ).hide();
                }
            }
        });


    }

    $(document).ready(function() {
        <?php echo 'initCancelJS(\'',$buttonSelector,'\', ',$appendId,');'; ?>
    });
</script>
<div id="<?php echo $popupId; ?>" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
        <h3><?php echo __($title); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __($description); ?></p>
    </div>
    <div class="modal-footer">
        <a href="#" id="<?php echo $buttonId; ?>" data-target="<?php echo Router::url($cancelUrl); ?>" class="btn danger"><?php echo __('Yes'); ?></a>
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('No'); ?></button>
    </div>
</div>