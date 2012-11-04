<script type="text/javascript">
    $(document).ready(function(){

        //copy subject it to popup
        $(".copyDataId").click(function () {
            $($(this).data('hidden-input')).val($(this).data('id'))
        });

        $('#makeOfferForlive').on('shown', function () {

            //Reset form
            $("#offer-teacher-lesson option").remove();//Remove old TeacherLessons
            $('#offer-teacher-lesson-group').hide();
            $('#offer-datetime-group').hide();

            /* Toggle Datetime - to - TeacherLessons */
            $('#show-offer-datetime-group').click(function(){
                showError('#makeOfferForlive .modal-body'); //Remove old alert msg

                $('#offer-teacher-lesson-group').hide();
                $('#offer-datetime-group').show();
                $('#live-offer-by').val('datetime');
            });

            $('#show-offer-teacher-lesson-group').click(function(){
                showError('#makeOfferForlive .modal-body'); //Remove old alert msg

                //Check first if there are any teacher lesson options, if not, don't allow the switch and show error
                if($('#offer-teacher-lesson option').size()==0) {
                    showError('#makeOfferForlive .modal-body', 'You got no existing lessons to offer', '');

                    return false;
                }

                $('#offer-datetime-group').hide();
                $('#offer-teacher-lesson-group').show();
                $('#live-offer-by').val('teacher_lesson_id');
            });




            //Handle form submit
            $('#live-offer-form').submit(function() {

                if($('#offer-subject').val()==0) {
                    showError('#makeOfferForlive .modal-body', 'Please select a subject', '');
                    return false;
                }

                $.post(
                    '/Requests/offerSubject/live.json',
                    $(this).serialize(),
                    function(data){

                        if(data['response']['title'][0]=='Error') {
                            //Show error
                            var msg = '';
                            if(data['response']['validation_errors']) {
                                $.each(data['response']['validation_errors'], function(key, val) {
                                    msg += val[0] + '<br />';
                                });
                            }
                            showError('#makeOfferForlive .modal-body', data['response']['description'][0], msg);

                        } else {
                            $('#makeOfferForlive').modal('hide');
                        }

                    }
                );

                return false;
            });






            /* Check if the current user can offer anything */
            if(!$('#offer-subject option') || $('#offer-subject option').size()<=1) {
                $('#makeOfferForlive').modal('hide');
                showError('section .container-inner', 'You got no subjects to offer', '');


                /* Current user can offer */
            } else {

                //1. On select a subject
                $('#offer-subject').change(function(){
                    showError('#makeOfferForlive .modal-body'); //Remove old alert msg
                    $('#live-offer-by').val('');

                    //User selected the first option (not a asubject)
                    if($(this).val()==0) {
                        $('#offer-teacher-lesson-group').hide();
                        $('#offer-datetime-group').hide();


                        //User select a subject
                    } else {

                        //2. Load TeacherLessons for a given subject
                        $.get('/Home/getUpcomingOpenLessonForSubject/'+ $(this).val() + '/100/1.json').done(function(data){
                            //Remove old TeacherLessons
                            $("#offer-teacher-lesson option").remove();

                            //3. Check if there are any Techer lessons
                            if(data['response']['results']['0']) {

                                //4.1. Add Teacher lessons to option menu
                                $.each(data['response']['results'], function(key, val){
                                    $('#offer-teacher-lesson').append($("<option></option>").attr('value', val['TeacherLesson']['teacher_lesson_id']).text(val['TeacherLesson']['datetime']));
                                });

                                //Show teacher lesson
                                $("#offer-teacher-lesson-group").show();
                                //Hide datetime
                                $("#offer-datetime-group").hide();
                                $('#live-offer-by').val('teacher_lesson_id');


                                //4.2, No TeacherLessons - force datetime
                            } else {
                                //Hide teacher lesson
                                $("#offer-teacher-lesson-group").hide();
                                //Show datetime
                                $("#offer-datetime-group").show();
                                $('#live-offer-by').val('datetime');
                            }

                        });
                    }
                });

            }
        })


    });
</script>
<?php
$this->Form->create('UserLesson');

echo $this->Form->input('request_subject_id',  array('type'=>'hidden', 'id'=>'request_subject_id', 'value'=>$requestSubjectId, 'class'=>'request_subject_id'));
?>

<fieldset>

    <div class="control-group">
        <label class="control-label" for="offer-subject"><?php echo __('Select a subject'); ?> :</label>
        <div class="control control1">
            <?php echo $this->Form->input('subject_id',  array('label' => false, 'class'=>'x-large2', 'id'=>'offer-subject', 'div'=>false, 'options'=>$teacherSubjectsSuggestions)); ?>
        </div>
    </div>

    <div id="offer-teacher-lesson-group" style="display: none">
        <div class="control-group">
            <label class="control-label" for="offer-teacher-lesson"><?php echo __('Existing lessons'); ?> :</label>
            <div class="control control1">
                <?php echo $this->Form->input('teacher_lesson_id',  array('label' => false, 'class'=>'x-large2', 'id'=>'offer-teacher-lesson', 'div'=>false, 'options'=>array())); ?>
            </div>
        </div>
        <div class="control-group">
            <div class="control control1">
                <p><a href="#" id="show-offer-datetime-group"><?php echo __('Click here if you\'d like to suggest a different time'); ?></a></p>
            </div>
        </div>
    </div>

    <div id="offer-datetime-group" style="display: none">
        <div class="control-group">
            <label class="control-label" for="offer-datetime"><?php echo __('Select a datetime'); ?> :</label>
            <div class="control control1">
                <?php echo $this->Form->input('datetime', array('label' => false, 'type'=>'datetime', 'id'=>'offer-datetime')); ?>
            </div>
        </div>
        <div class="control-group">

            <div class="control control1">
                <p><a href="#" id="show-offer-teacher-lesson-group"><?php echo __('Click here if you\'d like to select an existing lesson'); ?></a></p>
            </div>
        </div>
    </div>

</fieldset>
<?php
$this->Form->end();
?>