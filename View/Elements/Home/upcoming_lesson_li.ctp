<?php
echo '
    <li>
        <a href="#" class="pull-left">',$this->Html->image($this->Layout->image($upcomingAvailableLesson['TeacherLesson']['image_source'], 58, 58), array('alt' => 'Topic image')),'</a>
        <div class="upcominglesson-textbox">
            <div class="pull-right">
                ',$this->Layout->priceTag($upcomingAvailableLesson['TeacherLesson']['price'], $upcomingAvailableLesson['TeacherLesson']['full_group_student_price'], 'no-margin-right order-price'),'
                <div class="clear"></div>
                ',$this->Html->link('Join',
                                    array(  'controller'=>'Order', 'action'=>'init', 'join', $upcomingAvailableLesson['TeacherLesson']['teacher_lesson_id']),
                                    array(  'class'=>'btn-color-gry move-right space35 centered space37 upcoming-lesson-join',
                                            'escape'=>false,
                                            'data-statistics'=>$this->Layout->subjectStatistics(array(), $upcomingAvailableLesson['TeacherLesson'])
                )),'
            </div>


            <div class="space36">
                ',$this->Html->link($upcomingAvailableLesson['TeacherLesson']['name'],
                                    array(  'controller'=>'Home', 'action'=>'teacherLesson', $upcomingAvailableLesson['TeacherLesson']['teacher_lesson_id']),
                                    array(  'class'=>'upcoming-lesson-open',
                                            'escape'=>false,
                                            'data-statistics'=>$this->Layout->subjectStatistics(array(), $upcomingAvailableLesson['TeacherLesson'])
                                    )),'
                <p class="space3">',__('Start'),' :',$upcomingAvailableLesson['TeacherLesson']['datetime'],'</p>
                <p>',__('Current students'),' ',$upcomingAvailableLesson['TeacherLesson']['num_of_students'],' of ',$upcomingAvailableLesson['TeacherLesson']['max_students'],'</p>

            </div>
        </div>
    </li>';
?>