<?php
$this->Html->scriptBlock('
    $(document).ready(function() {
        mixpanel.track("Requests. search load");

        $(\'body\').delegate(\'.lesson-box\', \'click\', function(event) {
            var trackData = $(this).data(\'statistics\');
            mixpanel.track("Requests. Search subject click", trackData);
        });

        $(\'.lesson-request-popup\').click(function() {
            mixpanel.track("Requests. Search lesson request click");
        });
        $(\'#search_form\').submit(function() {
            mixpanel.track("Requests. Search search submit");
        });
    });
    ', array('inline'=>false));

    echo $this->element('Home'.DS.'search', array('controller'=>'Requests', 'action'=>'searchRequest'));
    echo $this->element('Requests'.DS.'offer_popups');
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
                    } ?>
                    <!-- end of category filter -->
                    <div class="lesson-wrapper">
						<div class="paging"> 
							<ul class="lesson-container">
								<?php
								if($subjectsData) {
									foreach($subjectsData['records'] AS $newSubject) {
										$newSubject['WishList']['one_on_one_price'] = $newSubject['WishList']['1_on_1_price'];
										echo '<li class="cont-span4 spc space2">';

                                        echo $this->element('wish_list', array(   'wishListId'            =>$newSubject['WishList']['wish_list_id'],
                                                                                        'name'                  =>$newSubject['WishList']['name'],
                                                                                        'description'           =>$newSubject['WishList']['description'],
                                                                                        'avarageRating'         =>$newSubject['Student']['student_avarage_rating'],
                                                                                        'oneOnOnePrice'         =>$newSubject['WishList']['1_on_1_price'],
                                                                                        'imageSource'           =>$newSubject['WishList']['image_source'],
                                                                                        'lessonType'            =>$newSubject['WishList']['lesson_type'],
                                                                                        'wishData'              =>$newSubject['WishList'],
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
                                if(isSet($subjectsData['records']) && count($subjectsData['records'])>=$subjectSearchLimit) {
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