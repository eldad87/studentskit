<script type="text/javascript">
    $(document).ready(function() {

        initSubjectForm('#Subject1On1Price', '#SubjectLessonType',
                        '#SubjectMaxStudents', '#msDiv',
                        '#fgspDiv', '#SubjectFullGroupStudentPrice');
        <?php
            if(isSet($success)) {
                echo '$(\'#subject-request-popup\').modal(\'hide\');';
            }
        ?>

    });
</script>
<fieldset>
    <?php
    $this->Form->create('Subject');
    //echo $this->Form->hidden('subject_id');
    echo $this->Form->input('name', $this->Layout->styleForInput());
    echo $this->Form->input('description', $this->Layout->styleForInput(array('type'=>'textarea')));
    //echo $this->Form->input('subject_category_id', $this->Layout->styleForInput(array('options'=>$subjectCategories)));
    echo $this->Form->input('subject_category_id', $this->Layout->styleForInput(array('options'=>$subjectCategories)));

    echo $this->Form->input('language', $this->Layout->styleForInput(array('options'=>$languages)));
    echo $this->Form->input('lesson_type', $this->Layout->styleForInput(array('options'=>array(LESSON_TYPE_LIVE=>__('Live'), LESSON_TYPE_VIDEO=>__('Video')))));
    echo $this->Form->input('is_public', $this->Layout->styleForInput(array('options'=>array(SUBJECT_IS_PUBLIC_TRUE=>__('Yes'), SUBJECT_IS_PUBLIC_FALSE=>__('No')))));
    echo $this->Form->input('duration_minutes', $this->Layout->styleForInput(array('type'=>'number', 'min'=>10)));
    echo $this->Form->input('imageUpload', $this->Layout->styleForInput(array('type'=>'file')));

    echo $this->Form->input('1_on_1_price', $this->Layout->styleForInput(array('type'=>'number', 'min'=>0)));
    echo $this->Form->input('max_students', $this->Layout->styleForInput(array('type'=>'number', 'min'=>1, 'div'=>array('id'=>'msDiv', 'class'=>'control-group'))));

    echo $this->Form->input('full_group_student_price', $this->Layout->styleForInput(array( 'type'=>'number', 'min'=>0,
                                                                                            'div'=>array('style'=>'display:none', 'id'=>'fgspDiv', 'class'=>'control-group') ,
                                                                                            'tooltip'=>__('a max discount price for a full lesson, the discount will take place starting from 2 students and above'))));

    $this->Form->end();
    ?>
</fieldset>

<!--<h3>Subject</h3>
<?php
/*echo $this->Form->create('Subject', array('type' => 'file'));
echo $this->Form->hidden('subject_id');
echo $this->Form->input('name');
echo $this->Form->input('description', array('type'=>'textarea'));
echo $this->Form->input('subject_category_id', array('options'=>$subjectCategories));
echo '<br /><br /><br />';
echo $this->Form->input('language', array('options'=>$languages));
echo '<br /><br /><br />';
echo $this->Form->input('lesson_type', array('options'=>array(LESSON_TYPE_LIVE=>'live', LESSON_TYPE_VIDEO=>'video')));
echo '<br /><br /><br />';
echo $this->Form->input('duration_minutes');
echo $this->Form->input('imageUpload', array('type' => 'file'));
//echo $this->Form->input('category_id');
//echo $this->Form->input('catalog_id');
*/?>
<br />
<h3>Pricing</h3>
<?php
/*//pr($this->data['Subject']);
echo $this->Form->input('1_on_1_price');


echo $this->Form->input('max_students');
echo $this->Form->input('full_group_student_price');
/*if(isSet($groupPrice)) {
	echo 'Full Group Student Price: ',$groupPrice,'<br />';
}*/

/*echo $this->Form->submit('Save');
echo $this->Form->end();*/
?>
<br />
<?php
/*if(isSet($nextLessons)) {
	echo '<h3>Lessons</h3>';
	pr($nextLessons);
}
*/?>
<br />
<?php
/*if(isSet($fileSystem)) {
	echo '<h3>FileSystem</h3>';
	pr($fileSystem);
}
*/?>
<br />
<?php
/*if(isSet($tests)) {
	echo '<h3>Test</h3>';
	pr($tests);
}
*/?>

-->