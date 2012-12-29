<?php
/**
 * Provide $buttonSelector
 *
 * must have "data-user_lesson_id" - the user lesson id that is about to be schedule
 * can have "data-update-tooltip-after-schedule" - if tool tip need to be updated
 * can have "data-remove-element-after-schedule" - remove an element after success
 */
?>

<script type="text/javascript">
    $(document).ready(function() {

        //Unbind existing events
        $('<?php echo $buttonSelector; ?>').unbind();
        $('#schedule-form').unbind();

        $('<?php echo $buttonSelector; ?>').click(function(e){
            e.preventDefault();

            //Reset form's data-*
            resetData('#schedule-form');

            //Copy data-* and place it as hidden:input
            $('#schedule-form').data($(this).data());

            //AJAX - load the form with its data
            var url = $('#schedule-form').attr('action');
            url = jQuery.nano(url, $(this).data());
            $('#schedule-form').attr('action', url);

            $.ajax({
                url: url,
                type: 'get',
                dataType: 'html'

            }).done(function ( data ) {
                //Append data into form
                $('#schedule-form .modal-body').html(data);

                //Show popup
                $('#schedule-popup').modal('show');
            });


        });

        pfObj.loadForm('#schedule-form', '#schedule-form .modal-body', 'post');

    });
</script>
<div id="schedule-popup" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
        <h3><?php echo __('schedule'); ?></h3>
    </div>
    <?php echo $this->Form->create('TeacherLesson', array('class'=>'sk-form', 'id'=>'schedule-form', 'method'=>'post',
                                                            'url'=>array('controller'=>'Teacher', 'action'=>'scheduleTeacherLesson', '{subject_id}'))); ?>

    <div class="modal-body">

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Cancel'); ?></button>
        <button class="btn btn-primary"><?php echo __('Submit'); ?></button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>