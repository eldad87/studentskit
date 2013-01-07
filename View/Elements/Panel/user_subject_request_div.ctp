<div class="lesson-box space2" id="subject_id_<?php echo $subjectRequestData['Subject']['subject_id']; ?>">
    <div class="head-back radius1">
        <h1><?php echo $this->Layout->lessonTypeIcon($subjectRequestData['Subject']['lesson_type']); ?><strong><?php echo $subjectRequestData['Subject']['name']; ?></strong></h1>
        <div class="dropdown pull-right">
            <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                <i class="iconSmall-drop-arrow"></i>
            </a>
            <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                <li><a href="#" class="lesson-request"  data-subject_id="<?php echo $subjectRequestData['Subject']['subject_id']; ?>"
                       data-replace-with="subject_id_<?php echo $subjectRequestData['Subject']['subject_id']; ?>"
                       data-append-template="user-panel"><?php echo __('Edit'); ?></a></li>
                <li><a href="#" class="confirm-delete" data-cancel-prefix="subject_id" data-id="<?php echo $subjectRequestData['Subject']['subject_id']; ?>"><?php echo __('Cancel'); ?></a></li>
            </ul>
        </div>

    </div>
    <div class="lesson-box-content">
        <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($subjectRequestData['Subject']['image_source'], 72, 72), array('alt' => 'Lesson image')); ?></div>
        <div class="usr-text2">
            <p><?php echo $subjectRequestData['Subject']['description']; ?></p>
            <p class="space23"><?php echo $this->Html->link(__('To the subject'), array('controller'=>'Home',
                'action'=>'subjectRequest',
                $subjectRequestData['Subject']['subject_id'])); ?></p>
        </div>
    </div>
    <div class="lesson-box-footer radius2">

        <div class="pull-right space21 right-i-mar">
            <?php
                echo $this->Layout->toolTip($this->Layout->buildLessonTooltipHtml($subjectRequestData['Subject']), null, 'pull-right space23', 'tooltip_'.$subjectRequestData['Subject']['subject_id']);
                echo $this->Layout->priceTag($subjectRequestData['Subject']['1_on_1_price'], $subjectRequestData['Subject']['full_group_student_price'], 'price-tag-panel');
            ?>
        </div>
    </div> <!-- /lesson-box-footer -->
</div> <!-- /lesson-box -->