<div id="lesson-request-popup" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3>Lesson Request</h3>
    </div> <!-- /modal-header -->

    <?php echo $this->Form->create( 'Subject',
                                    array(  'class'=>'sk-form', 'type' => 'file', 'method'=>'post', 'id'=>'make-subject-request-form',
                                            'url'=>array('controller'=>'Requests', 'action'=>'makeRequest'))); ?>


        <div class="modal-body">


            <fieldset>
                <?php
                    //echo $this->Form->hidden('subject_id');
                    echo $this->Form->input('name', $this->Layout->styleForInput());
                    echo $this->Form->input('description', $this->Layout->styleForInput(array('type'=>'textarea')));
                    //echo $this->Form->input('subject_category_id', $this->Layout->styleForInput(array('options'=>$subjectCategories)));
                    echo $this->Form->input('language', $this->Layout->styleForInput(array('options'=>$languages)));
                    echo $this->Form->input('lesson_type', $this->Layout->styleForInput(array('options'=>array(LESSON_TYPE_LIVE=>__('Live'), LESSON_TYPE_VIDEO=>__('Video')))));
                    echo $this->Form->input('duration_minutes', $this->Layout->styleForInput(array('type'=>'number', 'min'=>10)));
                    echo $this->Form->input('imageUpload', $this->Layout->styleForInput(array('type'=>'file')));

                    echo $this->Form->input('1_on_1_price', $this->Layout->styleForInput(array('type'=>'number', 'min'=>0)));
                    echo $this->Form->input('max_students', $this->Layout->styleForInput(array('type'=>'number', 'min'=>1, 'div'=>array('id'=>'msDiv', 'class'=>'control-group'))));

                    echo $this->Form->input('full_group_student_price', $this->Layout->styleForInput(array( 'type'=>'number', 'min'=>0,
                                                                                                            'div'=>array('style'=>'display:none', 'id'=>'fgspDiv', 'class'=>'control-group') ,
                                                                                                            'tooltip'=>__('a max discount price for a full lesson, the discount will take place starting from 2 students and above'))));
                ?>
            </fieldset>
        </div> <!-- /modal-body -->

        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Close'); ?></button>
            <button class="btn btn-primary"><?php echo __('Add request'); ?></button>
        </div>

    <?php echo $this->Form->end(); ?>
</div>
