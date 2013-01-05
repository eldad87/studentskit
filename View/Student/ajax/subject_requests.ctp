<script type="text/javascript">
    $(document).ready(function(){
        //Activate tooltip
        initToolTips();
    });
</script>

<?php
////////////// Page 1 - start
if($page==1) {
?>
    <script type="text/javascript">
        $(document).ready(function(){
            var url = '/Student/subjectRequests/{limit}/{page}';
            lmObj.loadMoreButton('#user-lessons-requests-load-more', 'click', '#user-lessons-requests', url, {}, 'get', <? echo $limit; ?>);
            lmObj.setItemsCountSelector('#user-lessons-requests-load-more', '#user-lessons-requests div.lesson-box' );
        });
    </script>

    <p class="fontsize1 space8"><?php echo __('Here you can find lesson requests.'); ?></p>
    <?php
        echo $this->Layout->subjectRequestPopupButton(array('name'=>__('ADD'), 'class'=>'black-cent-butn2 add-blckbtn fontsize1 move-right'));
        echo $this->element('Home/subject_request_popup');

        echo $this->element('panel/cancel_popup', array('buttonSelector'=>'.confirm-delete',
                                                        'title'=>__('Cancel your subject request'),
                                                        'description'=>__('Do you want to proceed?'),
                                                        'cancelUrl'=>array('controller'=>'Teacher', 'action'=>'disableSubject', '{id}')));
    ?>

    <div class="add-sub pull-left space3" id="user-lessons-requests">

<?php
}

    foreach($response['response']['subjectRequests'] AS $subjectRequest) {
        /*
        echo 'Message: ',$upcomingLesson['UserLesson']['teacher_user_id'],'-',$upcomingLesson['UserLesson']['student_user_id'],'-',$upcomingLesson['UserLesson']['user_lesson_id'],'<br />';
        echo $this->Html->link('Cancel', array('controller'=>'Student','action'=>'cancelUserLesson', $upcomingLesson['UserLesson']['user_lesson_id']));
        */

        echo '<div class="lesson-box space2" id="subject_id_'.$subjectRequest['Subject']['subject_id'].'">
                <div class="head-back radius1">
                    <h1>'.$this->Layout->lessonTypeIcon($subjectRequest['Subject']['lesson_type']).'<strong>'.$subjectRequest['Subject']['name'].'</strong></h1>
                    <div class="dropdown pull-right">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                            <i class="iconSmall-drop-arrow"></i>
                        </a>
                        <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                            <li><a href="#" class="lesson-request" data-subject_id="'.$subjectRequest['Subject']['subject_id'].'">'.__('Edit').'</a></li>
                            <li><a href="#" class="confirm-delete" data-cancel-prefix="subject_id" data-id="'.$subjectRequest['Subject']['subject_id'].'">'.__('Cancel').'</a></li>
                        </ul>
                    </div>

                </div>
                <div class="lesson-box-content">
                    <div class="user-pic2">'.$this->Html->image($this->Layout->image($subjectRequest['Subject']['image_source'], 72, 72), array('alt' => 'Lesson image'/*, 'class'=>'border1'*/)).'</div>
                    <div class="usr-text2">
                        <p>'.$subjectRequest['Subject']['description'].'</p>
                        <p class="space23">'.$this->Html->link(__('To the subject'), array('controller'=>'Home',
                                                                                                        'action'=>'subjectRequest',
                                                                                                        $subjectRequest['Subject']['subject_id'])).'</p>
                    </div>
                </div>
                <div class="lesson-box-footer radius2">

                    <div class="pull-right space21 right-i-mar">
                        '.$this->Layout->toolTip($this->Layout->buildLessonTooltipHtml($subjectRequest['Subject']), null, 'pull-right space23', 'tooltip_'.$subjectRequest['Subject']['subject_id']).'
                        '.$this->Layout->priceTag($subjectRequest['Subject']['1_on_1_price'], $subjectRequest['Subject']['full_group_student_price'], 'price-tag-panel').'
                        <!-- <a href="#" class=" pull-right space23"><i class="iconSmall-info r-mor-none"></i></a> -->
                    </div>
                </div> <!-- /lesson-box-footer -->
            </div> <!-- /lesson-box -->';
    }


////////////// Page 1 - start
if($page==1) {
    ?>
    </div>

    <?php
    if(count($response['response']['subjectRequests'])>=$limit) {
        echo '<a href="#" class="more radius3 gradient2 space8" id="user-lessons-requests-load-more"><strong>', __('Load More') ,'</strong><i class="iconSmall-more-arrow"></i></a>';
    }
}
////////////// Page 1 - end
?>
