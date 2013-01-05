<?php
echo $this->element('Home/search', array('controller'=>'Requests'));
echo $this->element('Requests/offer_popups');
?>

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

                <?php
                echo $this->Layout->subjectRequestPopupButton();
                echo $this->element('Home/subject_request_popup');
                ?>
                <div class="pull-left space6">
                    <!-- category filter -->
                    <?php if($subjectsData) {
                        echo $this->element('Home/facet_subject_categories', array('facet_categories'=>$subjectsData['categories']));
                    } ?>
                    <!-- end of category filter -->
                    <div class="lesson-wrapper">
						<div class="paging"> 
							<ul class="lesson-container">
								<?php
								if($subjectsData) {
									foreach($subjectsData['subjects'] AS $newSubject) {
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
							</ul>
						</div>
                        <!-- pager -->
                        <div class="fullwidth pull-left">
                            <div class="popmargin">
                                <?php
                                if(isSet($subjectsData['subjects']) && count($subjectsData['subjects'])>=$subjectSearchLimit) {
                                    echo '<a href="#" class="more radius3 gradient2 space9 pull-left search-load-more"><strong>',__('Load More'),'</strong><i class="iconSmall-more-arrow"></i></a>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- /cont-span8 -->
        </div> <!-- /row -->
    </div>
</Section>