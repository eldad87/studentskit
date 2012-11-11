<?php
/**
 * Provide $buttonSelector
 *
 * must have "data-user_lesson_id" - the user lesson id that is about to be negotiate
 */
?>

<script type="text/javascript">
    function initTeacherAboutVideoJS() {

        //Unbind existing events
        $(<?php echo $buttonSelector; ?>).unbind();
        $('#teacher-about-video-form').unbind();

        $('<?php echo $buttonSelector; ?>').click(function(e){
            e.preventDefault();

            //Reset form's data-*
            resetData('#teacher-about-video-form');

            //Copy data-* and place it as hidden:input
            $('#teacher-about-video-form').data($(this).data());

            //Build form URL
            formData = $('#teacher-about-video-form').data();
            url = $('#teacher-about-video-form').attr('action');
            url = jQuery.nano(url, formData)


            $.ajax({
                url: url,
                type: 'get',
                dataType: 'html'

            }).done(function ( data ) {
                    //Append data into form
                    $('#teacher-about-video-form .modal-body').html(data);


                    //Append data-* as hidden:input
                    $.each(formData, function(key, val){
                        $('<input>').attr('type','hidden').attr('name', key).attr('value', val).appendTo('#teacher-about-video-form');
                    });

                    //Append ajax form
                    $('#teacher-about-video-form').ajaxForm({
                        // target identifies the element(s) to update with the server response
                        target: '#teacher-about-video-form .modal-body',
                        url: url,

                        // success identifies the function to invoke when the server response
                        // has been received; here we apply a fade-in effect to the new content
                        success: function() {
                            $('#teacher-about-video-form .modal-body').fadeIn('slow');
                        }
                    });

                    //Show popup
                    $('#teacher-about-video-popup').modal('show');
                });


        });

    }

    $(document).ready(function() {
        initTeacherAboutVideoJS();
    });
</script>
<div id="teacher-about-video-popup" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
        <h3><?php echo __('Teacher about video'); ?></h3>
    </div>
    <?php echo $this->Form->create('TeacherAboutVideo', array('class'=>'sk-form', 'id'=>'teacher-about-video-form', 'method'=>'post', 'type' => 'file',
                                                                'url'=>array('controller'=>'Teacher', 'action'=>'aboutVideo', '{teacher_about_video_id}'))); ?>

    <div class="modal-body">

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Cancel'); ?></button>
        <button class="btn btn-primary"><?php echo __('Submit'); ?></button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>