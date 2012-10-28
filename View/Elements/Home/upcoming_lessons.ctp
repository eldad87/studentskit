<div class="student-main-box radius3 space25">
    <h5 class="fullwidth pad8 pull-left"><strong>Upcoming lessons</strong></h5>
    <ul class="subject-morelesson">
        <?php
        foreach($upcomingAvailableLessons AS $upcomingAvailableLesson) {
            echo '<li>
                                <a href="#" class="pull-left">',$this->Html->image($this->Layout->image($upcomingAvailableLesson['TeacherLesson']['image_source'], 58, 58), array('alt' => 'Topic image')),'</a>
                                <div class="upcominglesson-textbox">
                                    <div class="pull-right btn-width">
                                        <div class="price-tag space25 order-price"><span>',$this->Layout->priceTag($upcomingAvailableLesson['TeacherLesson']['1_on_1_price'], $upcomingAvailableLesson['TeacherLesson']['full_group_student_price']),'</span></div>
                                        ',$this->Html->link('Join', array('controller'=>'Order', 'action'=>'init', 'join', $upcomingAvailableLesson['TeacherLesson']['teacher_lesson_id']),
                array('class'=>'btn-color-gry move-right space35 centered space37')),'
                                    </div>

                                    <div class="space36">
                                        ',$this->Html->link($upcomingAvailableLesson['TeacherLesson']['name'], array('controller'=>'Home', 'action'=>'teacherLesson', $upcomingAvailableLesson['TeacherLesson']['teacher_lesson_id'])),'
                                        <p class="space3">Start :',$upcomingAvailableLesson['TeacherLesson']['datetime'],'</p>
                                        <p>Current student ',$upcomingAvailableLesson['TeacherLesson']['num_of_students'],' of ',$upcomingAvailableLesson['TeacherLesson']['max_students'],'</p>

                                    </div>
                                </div>
                            </li>';

        }
        ?>
    </ul>
</div>
<a href="#" class="more radius3 gradient2 space9 pull-left"><strong>Load More</strong><i class="iconSmall-more-arrow"></i></a>