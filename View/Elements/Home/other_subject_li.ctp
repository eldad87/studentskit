<?php
echo '<li>',
$this->Html->image($this->Layout->image($teacherSubject['image_source'], 128, 95), array('alt' => 'Topic image')),

$this->Html->link('<strong>'.$teacherSubject['name'].'</strong>',
    array('controller'=>'Home', 'action'=>'teacherSubject', $teacherSubject['subject_id']),
    array('escape'=>false, 'class'=>'fontsize1')),'
                    <div class="pull-right">
                    ',$this->Layout->priceTag($teacherSubject['1_on_1_price'], $teacherSubject['full_group_student_price']),'
                    </div>
                </li>';
?>