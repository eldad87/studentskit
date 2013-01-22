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
            var url = '/Teacher/lessonsArchive/{limit}/{page}';
            lmObj.loadMoreButton('#teacher-lessons-archive-load-more', 'click', '#teacher-lessons-archive', url, {}, 'get', <? echo $limit; ?>);
            lmObj.setItemsCountSelector('#teacher-lessons-archive-load-more', '#teacher-lessons-archive div.lesson-box' );
        });
    </script>

    <p class="fontsize1 space8"><?php echo __('Here you can find all your past lessons.'); ?></p>

    <div class="add-sub pull-left space3" id="teacher-lessons-archive">
    <?php
////////////// Page 1 - ends
}

    foreach($response['response']['archiveLessons'] AS $archiveLessons) {
        $toTheLessonLink = false;

        //Lesson took place
        if($archiveLessons['TeacherLesson']['is_deleted']==0) {
            $toTheLessonLink = $this->Html->link(__('Lesson page'), array('controller'=>'Lessons',
                                                                            'action'=>($archiveLessons['TeacherLesson']['lesson_type']==LESSON_TYPE_LIVE ? 'index' : 'video'),
                                                                            $archiveLessons['TeacherLesson']['teacher_lesson_id']));

        }

        $toSubject = $this->Html->link(__('To Subject'),
                                        $this->Layout->getOrganizerUrl('/Teacher/subjects/2/1/'.$archiveLessons['TeacherLesson']['subject_id']));


        $toBilling = false;
        if($archiveLessons['TeacherLesson']['payment_status']==PAYMENT_STATUS_DONE ||
            $archiveLessons['TeacherLesson']['payment_status']==PAYMENT_STATUS_PARTIAL) {

            $toBilling = $this->Html->link(__('To Billing'),
                $this->Layout->getOrganizerUrl('/Billing/index/2/1/'.$archiveLessons['TeacherLesson']['teacher_lesson_id']));

        }


        echo '<div class="lesson-box space2">
                <div class="head-back radius1">
                    <h1>'.$this->Layout->lessonTypeIcon($archiveLessons['TeacherLesson']['lesson_type']).
                        $this->Time->niceShort($archiveLessons['TeacherLesson']['datetime']).' -  <strong>'.$archiveLessons['TeacherLesson']['name'].'</strong></h1>';
                        ?>
                      <div class="dropdown pull-right">
                          <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                              <i class="iconSmall-drop-arrow"></i>
                          </a>
                          <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                            <?php
                            if($toTheLessonLink) {
                                echo '<li>',$toTheLessonLink,'</li>';
                            }
                            if($toBilling) {
                                echo '<li>',$toBilling,'</li>';
                            }
                            ?>
                            <li><?php echo $toSubject ?></li>
                          </ul>
                      </div>
                      <?php

            echo '
                </div>
                <div class="lesson-box-content">
                    <div class="user-pic2">'.$this->Html->image($this->Layout->image($archiveLessons['TeacherLesson']['image_source'], 72, 72), array('alt' => 'Lesson image')).'</div>
                    <div class="usr-text2">
                        <p>'.$archiveLessons['TeacherLesson']['description'].'</p>
                        '.($toTheLessonLink ? '<p class="space23">'.$toTheLessonLink.'</p>' : null).'
                    </div>
                </div>
                <div class="lesson-box-footer radius2">
                    <div class="pull-left star">
                        '.$this->Layout->ratingNew($archiveLessons['Subject']['avarage_rating'], false, 'pull-left pad8').'
                    </div>
                    <div class="pull-right space21 right-i-mar">
                        '.$this->Layout->toolTip($this->Layout->buildLessonTooltipHtml($archiveLessons['TeacherLesson']), null, 'pull-right space23', 'tooltip_'.$archiveLessons['TeacherLesson']['teacher_lesson_id']).'
                        '.$this->Layout->priceTag($archiveLessons['TeacherLesson']['1_on_1_price'], $archiveLessons['TeacherLesson']['full_group_student_price'], 'price-tag-panel').'
                    </div>
                </div> <!-- /lesson-box-footer -->
            </div> <!-- /lesson-box -->';
    }

////////////// Page 1 - start
if($page==1) {
    ?>
    </div>
<?php
    if(count($response['response']['archiveLessons'])>=$limit) {
        echo '<a href="#" class="more radius3 gradient2 space8" id="teacher-lessons-archive-load-more"><strong>', __('Load More') ,'</strong><i class="iconSmall-more-arrow"></i></a>';
    }
}
////////////// Page 1 - end
