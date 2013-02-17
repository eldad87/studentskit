<?php
    echo $this->element('Home'.DS.'search', array('controller'=>'Requests'));
    echo $this->element('Requests'.DS.'offer_popups');
?>
<Section class="container">
    <div class="container-inner">
        <div class="row">

                <h2 class="pull-left"><i class="iconBig-about space1"></i><?php echo __('Newest requests'); ?></h2>
                <?php
                    echo $this->Layout->subjectRequestPopupButton();
                    echo $this->element('Home'.DS.'subject_request_popup');
                ?>

                <ul class="row">
                    <?php
                    if($newSubjects) {
                        foreach($newSubjects AS $newSubject) {
                            //Home
                            $newSubject['Subject']['one_on_one_price'] = $newSubject['Subject']['1_on_1_price'];
                            echo '<li class="cont-span4 spc space2">';
                            echo $this->element('subject_request', array(   'subjectId'             =>$newSubject['Subject']['subject_id'],
                                                                            'name'                  =>$newSubject['Subject']['name'],
                                                                            'description'           =>$newSubject['Subject']['description'],
                                                                            'avarageRating'         =>$newSubject['Subject']['avarage_rating'],
                                                                            'oneOnOnePrice'         =>$newSubject['Subject']['1_on_1_price'],
                                                                            'fullGroupStudentPrice' =>$newSubject['Subject']['full_group_student_price'],
                                                                            'imageSource'           =>$newSubject['Subject']['image_source'],
                                                                            'lessonType'            =>$newSubject['Subject']['lesson_type'],
                                                                            'tooltipData'           =>$newSubject['Subject'],
                            ));
                            echo '</li>';
                        }
                    }
                    ?>
                </ul> <!-- /row -->

             <!-- /cont-span8 -->
        </div>
    </div>
</Section>