<?php
echo $this->element('panel/cancel_popup', array('buttonSelector'=>'.confirm-delete',
    'title'=>__('Cancel your lesson'),
    'description'=>__('This procedure may be irreversible.
                       Do you want to proceed?'),
    'cancelUrl'=>array('controller'=>'Teacher', 'action'=>'disableSubject', '{id}')));
?>

<div class="cont-span15 cbox-space">
    <p class="fontsize1 space8"><?php echo __('Here you can find all your subjects.'); ?></p>

    <?php
    echo $this->Html->link('ADD', '#', array( 'class'=>'black-cent-butn2 add-blckbtn fontsize1 move-right lesson-request load2',
                                                                'rel'=>Router::url(array('controller'=>'Teacher', 'action'=>'manageSubject'))));
?>

    <script type="text/javascript">
        $(document).ready(function(){
            //Activate tooltip
            initToolTips();

            initMenuLinks();
        });
    </script>

    <div class="add-sub pull-left space3">
        <?php
        foreach($response['response']['subjects'] AS $subject) {
            echo '<div class="lesson-box space2" id="subject_id_'.$subject['Subject']['subject_id'].'">
                    <div class="head-back radius1">
                        <h1>'.$this->Layout->lessonTypeIcon($subject['Subject']['lesson_type']).
                            '<strong>'.$subject['Subject']['name'].'</strong></h1>
                        <div class="dropdown pull-right">
                            <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                                <i class="iconSmall-drop-arrow"></i>
                            </a>
                            <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
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
                            '.$this->Layout->ratingNew($subject['Subject']['avarage_rating'], false, 'pull-left pad8').'
                        </div>
                        <div class="pull-right space21 right-i-mar">
                            '.$this->Layout->toolTip($this->Layout->buildLessonTooltipHtml($subject['Subject']), null, 'pull-right space23', 'tooltip_'.$subject['Subject']['subject_id']).'
                            '.$this->Layout->priceTag($subject['Subject']['1_on_1_price'], $subject['Subject']['full_group_student_price'], 'price-tag-panel').'
                            <!-- <a href="#" class=" pull-right space23"><i class="iconSmall-info r-mor-none"></i></a> -->
                        </div>
                    </div> <!-- /lesson-box-footer -->
                </div> <!-- /lesson-box -->';
        }
        ?>

    </div>
</div>