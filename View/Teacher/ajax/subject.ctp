<script type="text/javascript">
    $(document).ready(function() {

        <?php
        if($subjectId && isSet($enableNextStep)) {
            //Add subject id to the links
            if($isNewSave) {
                echo "
                var url = $.bbq.getState( '#sub-area' );
                var containerData = $('#sub-area').data( 'bbq' );

                $('.load3:visible').each(function(){
                            var rel = $(this).attr('rel');
                            var newUrl = rel + '/' + $subjectId;

                            //Replace cache keys
                            if(containerData.cache[ url ]) {
                                containerData.cache[ newUrl ] = containerData.cache[ url ];
                                delete containerData.cache[ url ];
                            }
                            $(this).attr('rel', newUrl);
                        });";
            }

            //Enable the meeting tab
            echo "$('#meetingTab:visible').removeClass('disable');";

            echo 'initTabs();';
            echo '$(\'#meetingTab:visible a\').click();';
        }
        ?>





        //pfObj.loadForm('#subject-form', '#sub-area', 'post');
        $('#subject-form:visible').unbind();
            $('#subject-form:visible').ajaxForm({
            // target identifies the element(s) to update with the server response
            target: '#sub-area div:visible',

            // success identifies the function to invoke when the server response
            // has been received; here we apply a fade-in effect to the new content
            success: function() {
                $('#sub-area div:visible').fadeIn('slow');
            }
        });


        /*initSubjectForm('#SubjectPrice', '#SubjectLessonType',
            '#SubjectMaxStudents', '#msDiv',
            '#fgspDiv', '#SubjectFullGroupStudentPrice', '#durationDiv');*/




        initSubjectAddForm(
            '#SubjectIsPublic',
            '#SubjectLessonType',
            '#SubjectPrice',
            '#SubjectMaxStudents'
        );


        initToolTips();
    });
</script>

<div class="cont-span6 cbox-space">
    <fieldset>
        <?php
        echo $this->Form->create( 'Subject',
                                    array(  'class'=>'sk-form', 'type' => 'file', 'method'=>'post', 'id'=>'subject-form',
                                            'url'=>array('controller'=>'Teacher', 'action'=>'subject', $subjectId)));

        echo $this->Form->input('name', $this->Layout->styleForInput());

        if(!$subjectId) {
            echo $this->Form->input('next', $this->Layout->styleForInput(array('type'=>'hidden', 'value'=>1)));
        }

        echo $this->Form->input('description', $this->Layout->styleForInput(array('type'=>'textarea')));
        echo $this->Form->input('category_id', $this->Layout->styleForInput(array('options'=>$subjectCategories)));

        echo $this->Form->input('language', $this->Layout->styleForInput(array('options'=>$languages)));

        echo $this->Form->input('image_source', $this->Layout->styleForInput(array('type'=>'file', 'label'=>array('class'=>'control-label', 'text'=>__('Image')))));

        echo $this->Form->input('video_source', $this->Layout->styleForInput(array('type'=>'file', 'label'=>array('class'=>'control-label', 'text'=>__('Preview video')))));


        if(!isSet($lessonType) || empty($lessonType)) {
            echo $this->Form->input('lesson_type', $this->Layout->styleForInput(array('options'=>array(LESSON_TYPE_LIVE=>__('Live'), LESSON_TYPE_VIDEO=>__('Video')))));
        } else {
            echo '<div class="control-group">
                    <label for="SubjectImageSource" class="control-label">',__('Lesson Type'),'</label>
                    <div class="control control1"><p class="space3">',strtoupper(__($lessonType)),'</p></div>
            </div>';
            echo $this->Form->hidden('lesson_type');
        }

        echo $this->Form->input('duration_minutes', $this->Layout->styleForInput(array('type'=>'number', 'min'=>4, 'div'=>array('id'=>'durationDiv', 'class'=>'control-group'))));

        echo $this->Form->input('is_public',  $this->Layout->styleForInput(
            array('options'=>array(
                    SUBJECT_IS_PUBLIC_TRUE  => __('Yes, Users may order this subject as standalone'),
                    SUBJECT_IS_PUBLIC_FALSE => __('No, I will use this subject in my courses only')
                ),
                'type'=>'select')
        ));


        echo '<div id="publicSettingsDiv">';

            echo $this->Form->input('price', $this->Layout->styleForInput(array('type'=>'number', 'min'=>0, 'step'=>'any')));


            echo '<div id="maxStudentsAndDiscountDiv">';
                echo $this->Form->input('max_students', $this->Layout->styleForInput(array('type'=>'number', 'min'=>1, 'div'=>array('class'=>'control-group'))));

                echo $this->Form->input('full_group_student_price', $this->Layout->styleForInput(array( 'type'=>'number', 'min'=>0, 'step'=>'any',
                                                                                                        'label'=>array('class'=>'control-label', 'text'=>__('Volume Discount')),
                                                                                                        'div'=>array('style'=>'display:none', 'id'=>'discountPriceDiv', 'class'=>'control-group') ,
                                                                                                        'tooltip'=>__('The max discount for when the lesson is full. The discount is relative to the amount of students and will take affect starting from the 2nd student. Leave BLANK for no discount.'),
                                                                                                        'tooltip_class'=>'pull-right space3'
                )));
            echo '</div>';

        echo '</div>';
        ?>
        <div class="control-group control2">
            <label class="control-label"></label>
            <div class="control">
                <button class="btn-blue pull-right" type="Save"><?php echo ($creationStage==CREATION_STAGE_NEW ? __('Save &amp; Next') : __('Save')); ?></button>
            </div>
        </div>
        <?php
        echo $this->Form->end();
        ?>
    </fieldset>
</div>