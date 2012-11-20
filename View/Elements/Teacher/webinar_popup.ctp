<?php
/**
 * Provide $buttonSelector
 * must have "data-id" - the lesson id
 *
 * Provide $lessonType - subject|video|live
 */
?>

<script type="text/javascript">
    $(document).ready(function() {
        //Change the popup width
        $('#webinar-popup').css({
                'width': function () {
                    return '940px';
                },
                'margin-left': function () {
                    return -($(this).width() / 2);
                }
            });


        //Unbind existing events
        $('<?php echo $buttonSelector; ?>').unbind();

        $('<?php echo $buttonSelector; ?>').click(function(e){
            e.preventDefault();

            $.ajax({
                url: $(this).attr('href'),
                type: 'get',
                dataType: 'html'

            }).done(function ( data ) {
                    //Append data into form
                    $('#webinar-popup .modal-body').html(data);

                    //Show popup
                    $('#webinar-popup').modal('show');
                });

            return false;
        });

    });
</script>
<div id="webinar-popup" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
        <h3><?php echo __('Webinar'); ?></h3>
    </div>


    <div class="modal-body">

    </div>

    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Cancel'); ?></button>
        <button class="btn btn-primary"><?php echo __('Submit'); ?></button>
    </div>
</div>