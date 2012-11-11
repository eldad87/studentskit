<?php
/**
 * Provide $buttonSelector
 *
 * must have "data-user_lesson_id" - the user lesson id that is about to be negotiate
 * can have "data-update-tooltip-after-negotiate" - if tool tip need to be updated
 * can have "data-remove-element-after-negotiate" - remove an element after success
 */
?>

<script type="text/javascript">
    function initCertificateJS() {
        $('<?php echo $buttonSelector; ?>').click(function(e){
            e.preventDefault();

            //Copy data-* and place it as hidden:input
            $('#certificate-form').data($(this).data());

            //Build form URL
            formData = $('#certificate-form').data();
            url = $('#certificate-form').attr('action');
            url = jQuery.nano(url, formData)


            $.ajax({
                url: url,
                type: 'get',
                dataType: 'html'

            }).done(function ( data ) {
                    //Append data into form
                    $('#certificate-form .modal-body').html(data);


                    //Append data-* as hidden:input
                    $.each(formData, function(key, val){
                        $('<input>').attr('type','hidden').attr('name', key).attr('value', val).appendTo('#certificate-form');
                    });

                    //Append ajax form
                    $('#certificate-form').ajaxForm({
                        // target identifies the element(s) to update with the server response
                        target: '#certificate-form .modal-body',
                        url: url,

                        // success identifies the function to invoke when the server response
                        // has been received; here we apply a fade-in effect to the new content
                        success: function() {
                            $('#certificate-form .modal-body').fadeIn('slow');
                        }
                    });

                    //Show popup
                    $('#certificate-popup').modal('show');
                });


        });

        //pfObj.loadForm('#certificate-form', '#certificate-form .modal-body', 'post');

    }

    $(document).ready(function() {
        initCertificateJS();
    });
</script>
<div id="certificate-popup" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
        <h3><?php echo __('Negotiate'); ?></h3>
    </div>
    <?php echo $this->Form->create('TeacherCertificate', array('class'=>'sk-form', 'id'=>'certificate-form', 'method'=>'post', 'type' => 'file',
                                                            'url'=>array('controller'=>'Teacher', 'action'=>'certificate', '{teacher_certificate_id}'))); ?>

    <div class="modal-body">

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Cancel'); ?></button>
        <button class="btn btn-primary"><?php echo __('Submit'); ?></button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>