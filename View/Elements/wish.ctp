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
        '.$this->Layout->ratingNew($averageRating, false, 'pull-left space3').'
        <div class="pull-right">
            '.$this->Layout->priceTag($oneOnOnePrice).'
            '.$this->Layout->toolTip($this->Layout->buildLessonTooltipHtml($wishData)).'
        </div>
    </div>
</div>', array('controller'=>'Requests', 'action'=>'makeOffer', $wishListId),
            $this->Layout->requireLogin(array(  'data-toggle'=>'modal', 'data-target'=>'#makeOffer',
                                                'data-id'=>$wishListId, 'data-hidden-input'=>'#wish_list_id',
                                                'class'=>'copyDataId', 'escape'=>false,
                                                'data-statistics'=>$this->Layout->subjectStatistics($wishData)

            )));
?>