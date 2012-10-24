<?php echo $this->element('Home/search'); ?>

<!-- Containeer
================================================== -->
<Section class="container">
    <div class="container-inner">
        <div class="row">
            <div class="cont-span12">
                <p class="pull-left bodytop-leftlink">
                    <?php
                    if(isSet($subjectsData['breadcrumbs'])) {
                        echo $this->element('Home/subject_categories_breadcrumbs', array('subject_categories_breadcrumbs'=>$subjectsData['breadcrumbs'])),'<br /><br />';
                    }
                    ?>
                </p>
                <button type="button" class="btns btn-black pull-right space28"><?php echo $this->Html->link('Lesson Requests', array('controller'=>'Requests')); ?></button>
                <div class="pull-left space6">
                    <!-- category filter -->
                    <?php if($subjectsData) {
                        echo $this->element('Home/facet_subject_categories', array('facet_categories'=>$subjectsData['categories']));
                    } ?>
                    <!-- end of category filter -->
                    <div class="lesson-wrapper">
                        <ul class="lesson-container">
                            <?php
                            if($subjectsData) {
                                foreach($subjectsData['subjects'] AS $newSubject) {
                                    $newSubject['Subject']['one_on_one_price'] = $newSubject['Subject']['1_on_1_price'];
                                    echo '<li class="cont-span4 spc space2">';
                                    echo $this->element('subject', array(   'subjectId'            =>$newSubject['Subject']['subject_id'],
                                        'teacherUserId'         =>$newSubject['Subject']['user_id'],
                                        'teacherUsername'       =>$newSubject['Teacher']['username'],
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
                        </ul>
                        <!-- pager -->
                        <div class="pagination pull-right space1">
                            <ul>
                                <li class="disabled"><a href="#">&lt; &lt; Prev</a></li>
                                <li class="active"><a href="#">1</a></li>
                                <li><a href="#">2</a></li>
                                <li><a href="#">3</a></li>
                                <li><a href="#">4</a></li>
                                <li><a href="#">Next &gt; &gt;</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div> <!-- /cont-span8 -->
        </div> <!-- /row -->
    </div>
</Section>