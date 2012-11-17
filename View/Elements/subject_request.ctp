<?php
echo $this->Html->link('<div class="lesson-box">
    <h3 class="radius1">
    '.$this->Layout->lessonTypeIcon($lessonType).'
    <strong>'.String::truncate($this->Layout->formatTitle($name)).'</strong></h3>
    <div class="lesson-box-content">
        <div class="user-pic2">'.$this->Html->image($this->Layout->image($imageSource, 72, 72), array('alt' => 'Topic image')).'</div>
        <div class="usr-text2">
            <p>'.String::truncate(($description), 63, array('ending'=>'..', 'html'=>true)).'</p>
        </div>
    </div>
    <div class="lesson-box-footer radius2">
        '.$this->Layout->ratingNew($avarageRating, false, 'pull-left space3').'
        <div class="pull-right">
            '.$this->Layout->priceTag($oneOnOnePrice, $fullGroupStudentPrice).'
            '.$this->Layout->toolTip($this->Layout->buildLessonTooltipHtml($tooltipData)).'
        </div>
    </div>
</div>', array('controller'=>'Requests', 'action'=>'offerSubject', $subjectId),
            $this->Layout->requireLogin(array(  'data-toggle'=>'modal', 'data-target'=>'#makeOffer',
                                                'data-id'=>$subjectId, 'data-hidden-input'=>'#request_subject_id',
                                                'class'=>'copyDataId', 'escape'=>false)));
?>