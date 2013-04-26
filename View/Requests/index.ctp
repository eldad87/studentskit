<?php
$this->Html->scriptBlock('
    $(document).ready(function() {
        mixpanel.track("Requests. index load");

        $(\'.lesson-box\').click(function() {
            var trackData = $(this).data(\'statistics\');
            mixpanel.track("Requests. Index subject click", trackData);
        });
        $(\'.lesson-request-popup\').click(function() {
            mixpanel.track("Requests. Index lesson request click");
        });
        $(\'#search_form\').submit(function() {
            mixpanel.track("Requests. Index search submit");
        });
    });
    ', array('inline'=>false));

    echo $this->element('Home'.DS.'search', array('controller'=>'Requests', 'action'=>'searchRequest'));
    echo $this->element('Requests'.DS.'offer_popups');
?>
<Section class="container">
    <div class="container-inner">
        <div class="row">

                <h2 class="pull-left"><i class="iconBig-about space1"></i><?php echo __('Newest requests'); ?></h2>
                <?php
                    echo $this->Layout->wishPopupButton();
                    echo $this->element('Home'.DS.'wish_popup');
                ?>

                <ul class="row">
                    <?php
                    if($newWishList) {
                        foreach($newWishList AS $wishRequest) {
                            //Home
                            $wishData = $wishRequest['WishList'];
                            $wishData['one_on_one_price'] = $wishRequest['WishList']['1_on_1_price'];

                            echo '<li class="cont-span4 spc space2">';
                            echo $this->element('wish', array( 'wishListId'            =>$wishRequest['WishList']['wish_list_id'],
                                                                    'name'                  =>$wishRequest['WishList']['name'],
                                                                    'description'           =>$wishRequest['WishList']['description'],
                                                                    'averageRating'         =>$wishRequest['Student']['student_average_rating'],
                                                                    'oneOnOnePrice'         =>$wishRequest['WishList']['1_on_1_price'],
                                                                    'imageSource'           =>$wishRequest['WishList']['image_source'],
                                                                    'lessonType'            =>$wishRequest['WishList']['lesson_type'],
                                                                    'wishData'              =>$wishData
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