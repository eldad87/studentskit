<?php
echo '<li>',
$this->Html->image($this->Layout->image($teacherSubject['image_source'], 128, 95), array('alt' => 'Topic image')),

    '<div class="pull-right fullwidth">
                        ',$this->Layout->priceTag($teacherSubject['price'], $teacherSubject['full_group_student_price'], 'price-tag-float-right space25'),'
                        ',$this->Layout->lessonTypeIcon($lessonType, 'pull-left'),'
                    </div>',

$this->Html->link('<strong>'.$teacherSubject['name'].'</strong>',
    array('controller'=>'Home', 'action'=>'teacherSubject', $teacherSubject['subject_id']),
    array(  'escape'=>false,
            'class'=>'fontsize1 other-subject',
            'data-statistics'=>$this->Layout->subjectStatistics($teacherSubject, array())
    )),'



                </li>';
?>