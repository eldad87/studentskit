<!-- /reviews -->
<div class="lesson-box pad8 space4">
    <h3 class="radius1"><strong><?php echo __('What student says about me?'); ?></strong></h3>
    <div class="box-subject2 radius3 fix-height">
        <div class="reviews-by-students">
            <?php
            if($ratingByStudents) {
                foreach($ratingByStudents AS $ratingByStudent) {
                    echo $this->element('Home/reviews_by_students_div', array('ratingByStudent'=>$ratingByStudent));
                }
            }
            ?>
        </div>
    </div>
    <!-- /lesson-box -->
</div>
<a href="#" class="more radius3 gradient2 space8 reviews-by-students"><strong>Load More</strong><i class="iconSmall-more-arrow"></i></a>