<!-- /reviews -->
<div class="lesson-box pad8 space4">
    <h3 class="radius1"><strong><?php echo $title; ?></strong></h3>
    <div class="box-subject2 radius3">
        <div class="reviews-by-students">
            <?php
            $i = 0;
            if($ratingByStudents) {
                foreach($ratingByStudents AS $ratingByStudent) {
                    echo $this->element('Home'.DS.'reviews_by_students_div', array('ratingByStudent'=>$ratingByStudent, 'first'=>!$i++));
                }
            }
            ?>
        </div>
        <?php
        if(!$ratingByStudents) {
            echo '<p>',__('No reviews yes'),'</p>';
        }
        ?>
    </div>
    <!-- /lesson-box -->
</div>
<?php
if(count($ratingByStudents)>=$reviewsLimit) {
    echo '<a href="#" class="more radius3 gradient2 space8 reviews-by-students"><strong>',__('Load More'),'</strong><i class="iconSmall-more-arrow"></i></a>';
}