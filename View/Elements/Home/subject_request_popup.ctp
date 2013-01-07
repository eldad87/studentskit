<?php
/**
 * Provide $buttonSelector
 *
 * must have "data-subject_id" - in case of edit, add the subject id
 */
if(!isSet($buttonSelector)) {
    $buttonSelector = '.lesson-request';
}
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('body').undelegate('<?php echo $buttonSelector; ?>', 'click');
        $('body').delegate('<?php echo $buttonSelector; ?>', 'click', function(e) {
        //$('<?php echo $buttonSelector; ?>').click(function(e){
            e.preventDefault();

            //Copy data-* and place it as hidden:input
            $('#make-subject-request-form').data($(this).data());

            //AJAX - load the form with its data
            url = $('#make-subject-request-form').attr('action');
            $.ajax({
                url: jQuery.nano(url, $(this).data()),
                type: 'get',
                dataType: 'html'

            }).done(function ( data ) {
                    //Append data into form
                    $('#make-subject-request-form .modal-body').html(data);

                    //Show popup
                    $('#subject-request-popup').modal('show');
                });


        });

        pfObj.loadForm('#make-subject-request-form', '#make-subject-request-form .modal-body', 'post');

    });
</script>

<div id="subject-request-popup" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3><?php echo __('Lesson Request'); ?></h3>
    </div> <!-- /modal-header -->

    <?php echo $this->Form->create( 'Subject',
                                    array(  'class'=>'sk-form', 'type' => 'file', 'method'=>'post', 'id'=>'make-subject-request-form',
                                            'url'=>array('controller'=>'Requests', 'action'=>'makeRequest', '{subject_id}'))); ?>


        <div class="modal-body">
        </div> <!-- /modal-body -->

        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Close'); ?></button>
            <button class="btn btn-primary"><?php echo __('Save request'); ?></button>
        </div>

    <?php echo $this->Form->end(); ?>
</div>
