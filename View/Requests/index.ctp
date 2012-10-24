<?php echo $this->element('Home/search', array('controller'=>'Requests'));  ?>
<Section class="container">
    <div class="container-inner">
        <div class="row">
            <div class="cont-span8">
                <h2 class="pull-left"><i class="iconBig-about space1"></i>Newest subjects</h2>
                <button type="button" class="btns btn-black pull-right"><?php echo $this->Html->link('Lesson Requests', array('controller'=>'Requests')); ?></button>
                <ul class="row">
                    <?php
                    if($newSubjects) {
                        foreach($newSubjects AS $newSubject) {
                            $newSubject['Subject']['one_on_one_price'] = $newSubject['Subject']['1_on_1_price'];
                            echo '<li class="cont-span4 spc space2">';
                            echo $this->element('subject_request', array(   'subjectId'             =>$newSubject['Subject']['subject_id'],
                                                                            'teacherUserId'         =>$newSubject['Subject']['user_id'],
                                                                            'teacherUsername'       =>$newSubject['Student']['username'],
                                                                            'name'                  =>$newSubject['Subject']['name'],
                                                                            'description'           =>$newSubject['Subject']['description'],
                                                                            'avarageRating'         =>$newSubject['Subject']['avarage_rating'],
                                                                            'oneOnOnePrice'         =>$newSubject['Subject']['1_on_1_price'],
                                                                            'fullGroupStudentPrice' =>$newSubject['Subject']['full_group_student_price'],
                                                                            'imageSource'           =>$newSubject['Subject']['image_source'],
                            ));
                            echo '</li>';
                        }
                    }
                    ?>
                </ul> <!-- /row -->

            </div> <!-- /cont-span8 -->
        </div>
    </div>
</Section>