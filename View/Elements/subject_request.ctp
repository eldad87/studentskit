<?php
echo $this->Html->link('<div class="lesson-box">
    <h3 class="radius1"><strong>'.String::truncate($this->Layout->formatTitle($name)).'</strong></h3>
    <div class="lesson-box-content">
        <div class="user-pic2">'.$this->Html->image($this->Layout->image($imageSource, 72, 72), array('alt' => 'Topic image')).'</div>
        <div class="usr-text2">
            <p>'.String::truncate(($description), 63, array('ending'=>'..', 'html'=>true)).'</p>
        </div>
    </div>
    <div class="lesson-box-footer radius2">
        '.$this->Layout->rating($avarageRating).'
        <div class="pull-right">
            '.$this->Layout->priceTag($oneOnOnePrice, $fullGroupStudentPrice).'
            <a href="#"><i class="iconSmall-info space3"></i></a>
        </div>
    </div>
</div>', array('controller'=>'Requests', 'action'=>'offerSubject', $subjectId),
            $this->Layout->requireLogin(array(  'data-toggle'=>'modal', 'data-target'=>'#makeOfferForlive',
                                                'data-id'=>$subjectId, 'data-hidden-input'=>'#live_request_subject_id',
                                                'class'=>'copyDataId', 'escape'=>false)));
?>