<?php
/**
 * Provide $buttonSelector
 *
 * must have "data-user_lesson_id" - the user lesson id that is about to be negotiate
 * can have "data-remove-element-after-accept" - remove an element after success
 */
?>

<script type="text/javascript">
    $(document).ready(function() {

        //Unbind existing events
        $(<?php echo $buttonSelector; ?>).unbind();
        $('#accept-form').unbind();

        $('<?php echo $buttonSelector; ?>').click(function(e){
            e.preventDefault();

            //Reset form's data-*
            resetData('#accept-form');

            //Copy data-* and place it as hidden:input
            $('#accept-form').data($(this).data());

            //AJAX - load the form with its data
            url = $('#accept-form').attr('action');
            $.ajax({
                url: jQuery.nano(url, $(this).data()),
                type: 'get',
                dataType: 'html'

            }).done(function ( data ) {
                    //Append data into form
                    $('#accept-form .modal-body').html(data);

                    //Show popup
                    $('#accept-popup').modal('show');
                });


        });

        pfObj.loadForm('#accept-form', '#accept-form .modal-body', 'post');

    });
</script>
<div id="accept-popup" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
        <h3><?php echo __('Accept'); ?></h3>
    </div>
    <?php echo $this->Form->create('UserLesson', array('class'=>'sk-form', 'id'=>'accept-form', 'method'=>'post',
                                                        'url'=>array('controller'=>'Student', 'action'=>'acceptUserLesson', '{user_lesson_id}'))); ?>

    <div class="modal-body">

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Cancel'); ?></button>
        <button class="btn btn-primary"><?php echo __('Accept'); ?></button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>