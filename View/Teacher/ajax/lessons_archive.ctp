<p class="fontsize1 space8"><?php echo __('Here you can find all your past lessons.'); ?></p>

<script type="text/javascript">
    $(document).ready(function(){
        //Activate tooltip
        initToolTips();
    });
</script>
<div class="add-sub pull-left space3">

    <?php
    foreach($response['response']['archiveLessons'] AS $archiveLessons) {

        $toTheLessonLink = false;

        //Lesson took place
        if($archiveLessons['TeacherLesson']['is_deleted']==1) {
            $toTheLessonLink = $this->Html->link(__('Lesson page'), array('controller'=>'Lessons',
                                                                            'action'=>($archiveLessons['TeacherLesson']['lesson_type']==LESSON_TYPE_LIVE ? 'index' : 'video'),
                                                                            $archiveLessons['TeacherLesson']['teacher_lesson_id']));
        }

        echo '<div class="lesson-box space2">
                <div class="head-back radius1">
                    <h1>'.$this->Time->niceShort($archiveLessons['TeacherLesson']['datetime']).' -  <strong>'.$archiveLessons['TeacherLesson']['name'].'</strong></h1>
                    <div class="dropdown pull-right">
                        <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                            <i class="iconSmall-drop-arrow"></i>
                        </a>
                        '.($toTheLessonLink ? '<ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel"><li>'.$toTheLessonLink.'</li></ul>' : null).'
                    </div>
                </div>
                <div class="lesson-box-content">
                    <div class="user-pic2">'.$this->Html->image($this->Layout->image($archiveLessons['TeacherLesson']['image_source'], 72, 72), array('alt' => 'Lesson image'/*, 'class'=>'border1'*/)).'</div>
                    <div class="usr-text2">
                        <p>'.$archiveLessons['TeacherLesson']['description'].'</p>
                        '.($toTheLessonLink ? '<p class="space23">'.$toTheLessonLink.'</p>' : null).'
                    </div>
                </div>
                <div class="lesson-box-footer radius2">
                    <div class="pull-left star">
                        '.$this->Layout->ratingNew($archiveLessons['Student']['avarage_rating'], false, 'pull-left pad8').'
                    </div>
                    <div class="pull-right space21 right-i-mar">
                        '.$this->Layout->toolTip($this->Layout->buildLessonTooltipHtml($archiveLessons['TeacherLesson']), null, 'pull-right space23', 'tooltip_'.$archiveLessons['TeacherLesson']['teacher_lesson_id']).'
                        '.$this->Layout->priceTag($archiveLessons['TeacherLesson']['1_on_1_price'], $archiveLessons['TeacherLesson']['full_group_student_price'], 'price-tag-panel').'
                    </div>
                </div> <!-- /lesson-box-footer -->
            </div> <!-- /lesson-box -->';
    }
    ?>
</div>
