<script type="text/javascript">
    $(document).ready(function(){
        //Activate tooltip
        initToolTips();

        //initMenuLinks();

    });
</script>

<?php
////////////// Page 1 - start
if($page==1) {

    echo $this->element('Panel'.DS.'cancel_popup', array('buttonSelector'=>'.confirm-delete',
        'title'=>__('Cancel your lesson'),
        'description'=>__('This procedure may be irreversible.
                           Do you want to proceed?'),
        'cancelUrl'=>array('controller'=>'Teacher', 'action'=>'disableSubject', '{id}')));

    echo $this->element('Panel'.DS.'schedule_teacher_lesson_popup', array('buttonSelector'=>'.schedule'));
    ?>

    <script type="text/javascript">
        $(document).ready(function(){
            var url = '/Teacher/subjects/{limit}/{page}';
            lmObj.loadMoreButton('#teacher-subjects-load-more', 'click', '#teacher-subjects', url, {}, 'get', <?php echo $limit; ?>);
            lmObj.setItemsCountSelector('#teacher-subjects-load-more', '#teacher-subjects div.lesson-box' );
        });
    </script>


    <div class="cont-span15 cbox-space">
        <p class="fontsize1 space8"><?php echo __('Here you can find all your subjects.'); ?></p>

        <?php
        echo $this->Html->link('ADD', '#', array( 'class'=>'black-cent-butn2 add-blckbtn fontsize1 move-right lesson-request load2',
                                                                    'rel'=>Router::url(array('controller'=>'Teacher', 'action'=>'manageSubject'))));
        ?>

        <div class="add-sub pull-left space3" id="teacher-subjects">
<?php
}

        foreach($response['response']['subjects'] AS $subject) {
            echo '<div class="lesson-box space2" id="subject_id_'.$subject['Subject']['subject_id'].'">
                    <div class="head-back radius1">
                        <h1>'.$this->Layout->lessonTypeIcon($subject['Subject']['lesson_type']).
                            '<strong>'.$subject['Subject']['name'].'</strong></h1>
                        <div class="dropdown pull-right">
                            <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                                <i class="iconSmall-drop-arrow"></i>
                            </a>
                            <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">';

            if($subject['Subject']['lesson_type']==LESSON_TYPE_LIVE &&
                $subject['Subject']['is_public']==SUBJECT_IS_PUBLIC_TRUE) {
                echo '<li><a href="#" class="schedule" data-subject_id="'.$subject['Subject']['subject_id'].'">'.__('Schedule').'</a></li>';
            }
            echo '
                                <li><a href="#" class="load2" rel="'.Router::url(array('controller'=>'Teacher', 'action'=>'manageSubject', $subject['Subject']['subject_id'])).'">'.__('Manage').'</a></li>
                                <li><a href="#" class="confirm-delete" data-cancel-prefix="subject_id" data-id="'.$subject['Subject']['subject_id'].'">'.__('Disable').'</a></li>
                            </ul>
                        </div>



                    </div>
                    <div class="lesson-box-content">
                        <div class="user-pic2">'.$this->Html->image($this->Layout->image($subject['Subject']['image_source'], 72, 72), array('alt' => 'Lesson image')).'</div>
                        <div class="usr-text2">
                            <p>'.$subject['Subject']['description'].'</p>
                        </div>
                    </div>
                    <div class="lesson-box-footer radius2">
                        <div class="pull-left star">
                            '.$this->Layout->ratingNew($subject['Subject']['average_rating'], false, 'pull-left pad8').'
                        </div>
                        <div class="pull-right space21 right-i-mar">
                            '.$this->Layout->toolTip($this->Layout->buildLessonTooltipHtml($subject['Subject']), null, 'pull-right space23', 'tooltip_'.$subject['Subject']['subject_id']).'
                            '.$this->Layout->priceTag($subject['Subject']['price'], $subject['Subject']['bulk_price'], 'price-tag-panel').'
                            <!-- <a href="#" class=" pull-right space23"><i class="iconSmall-info r-mor-none"></i></a> -->
                        </div>
                    </div> <!-- /lesson-box-footer -->
                </div> <!-- /lesson-box -->';
        }

////////////// Page 1 - start
if($page==1) {
?>

        </div>
    </div>

<?php
    if(count($response['response']['subjects'])>=$limit) {
        echo '<a href="#" class="more radius3 gradient2 space8" id="teacher-subjects-load-more"><strong>', __('Load More') ,'</strong><i class="iconSmall-more-arrow"></i></a>';
    }
}
////////////// Page 1 - end



