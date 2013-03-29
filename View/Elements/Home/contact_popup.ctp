<?php
/**
 * Provide $buttonSelector
 *
 * must have "data-contact_id" - in case of edit, add the contact id
 */
if(!isSet($buttonSelector)) {
    $buttonSelector = '.contact-request';
}
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('body').undelegate('<?php echo $buttonSelector; ?>', 'click');
        $('body').delegate('<?php echo $buttonSelector; ?>', 'click', function(e) {
            e.preventDefault();

            //Copy data-* and place it as hidden:input
            $('#make-contact-request-form').data($(this).data());

            //AJAX - load the form with its data
            url = $('#make-contact-request-form').attr('action');
            $.ajax({
                url: jQuery.nano(url, $(this).data()),
                type: 'get',
                dataType: 'html'

            }).done(function ( data ) {
                    //Append data into form
                    $('#make-contact-request-form .modal-body').html(data);

                    //Show popup
                    $('#contact-request-popup').modal('show');
                });


        });

        pfObj.loadForm('#make-contact-request-form', '#make-contact-request-form .modal-body', 'post');

    });
</script>

<div id="contact-request-popup" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3><?php echo __('Send us a message'); ?></h3>
    </div> <!-- /modal-header -->

    <?php echo $this->Form->create( 'contact',
                                    array(  'class'=>'sk-form', 'type' => 'file', 'method'=>'post', 'id'=>'make-contact-request-form', 'novalidate'=>'novalidate',
                                            'url'=>array('controller'=>'Support', 'action'=>'contact', '?'=>array('topic'=>'{topic}', 'subject'=>'{subject}')))); ?>


        <div class="modal-body">
        </div> <!-- /modal-body -->

        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Close'); ?></button>
            <button class="btn btn-primary"><?php echo __('Send'); ?></button>
        </div>

    <?php echo $this->Form->end(); ?>
</div>
