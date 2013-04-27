<?php
    $this->Html->scriptBlock('
    $(document).ready(function() {
        mixpanel.track("Home. search load");

        $(\'body\').delegate(\'.subject-box\', \'click\', function(event) {
            var trackData = $(this).data(\'statistics\');
            mixpanel.track("Home. Search subject click", trackData);
        });

        $(\'.lesson-request-popup\').click(function() {
            mixpanel.track("Home. Search lesson request click");
        });
        $(\'#search_form\').submit(function() {
            mixpanel.track("Home. Search search submit");
        });
    });
    ', array('inline'=>false));

    echo $this->element('Home'.DS.'search');
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
                        echo $this->element('Home'.DS.'categories_breadcrumbs', array('categories_breadcrumbs'=>$subjectsData['breadcrumbs'])),'<br /><br />';
                    }
                    ?>
                </p>
                <?php
                    echo $this->Layout->wishPopupButton();
                    echo $this->element('Home'.DS.'wish_popup');
                ?>
                <div class="pull-left space6">
                    <!-- category filter -->
                    <?php if($subjectsData) {
                        echo $this->element('Home'.DS.'facet_categories', array('facet_categories'=>$subjectsData['categories']));
                    } else {
                        echo '<p>',__('No lessons found.'),'</p>';
                    } ?>
                    <!-- end of category filter -->
                    <div class="lesson-wrapper">
						<div class="paging"> 
							<ul class="lesson-container">
								<?php
								if($subjectsData) {
									foreach($subjectsData['records'] AS $newSubject) {
										$newSubject['records']['one_on_one_price'] = $newSubject['Subject']['price'];
										echo '<li class="cont-span4 spc space2">';

                                        echo $this->Html->link( $this->element( 'subject', array(
                                            'subjectId'             =>$newSubject['Subject']['subject_id'],
                                            'teacherUserId'         =>$newSubject['Subject']['user_id'],
                                            'teacherUsername'       =>$newSubject['Teacher']['username'],
                                            'name'                  =>$newSubject['Subject']['name'],
                                            'description'           =>$newSubject['Subject']['description'],
                                            'averageRating'         =>$newSubject['Subject']['average_rating'],
                                            'price'                 =>$newSubject['Subject']['price'],
                                            'fullGroupStudentPrice' =>$newSubject['Subject']['full_group_student_price'],
                                            'imageSource'           =>$newSubject['Subject']['image_source'],
                                            'lessonType'            =>$newSubject['Subject']['lesson_type'],
                                            'subjectData'           =>$newSubject['Subject'],
                                        )),
                                            array('controller'=>'Home', 'action'=>'teacherSubject', $newSubject['Subject']['subject_id']),
                                            array(  'escape'=>false,
                                                    'class'=>'subject-box',
                                                    'data-statistics'=>$this->Layout->subjectStatistics($newSubject['Subject'])
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