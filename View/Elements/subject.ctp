<div class="lesson-box">
    <h3 class="radius1"><strong><?php
        //Create a string - max 37 chars, camelize each word -> Abcd eFg -> Abcd Efg
        echo String::truncate(
            $this->Html->link($this->Layout->formatTitle($name),
                                array('controller'=>'Home', 'action'=>'teacherSubject', $subjectId),
                                array('class'=>'radius1')), 37, array('ending'=>'..', 'html'=>true));?></strong></h3>
    <div class="lesson-box-content">
        <div class="user-pic2">
            <?php
            echo $this->Html->image($this->Layout->image($imageSource, 72, 72), array('alt' => 'Topic image', 'url'=>array('controller'=>'Home', 'action'=>'teacherSubject', $subjectId)));
            ?>
        </div>
        <div class="usr-text2">
            <h4>by <?php echo $this->Html->link($this->Layout->formatTitle($teacherUsername), array('controller'=>'Home', 'action'=>'teacher', $teacherUserId)); ?></h4>
            <p><?php echo String::truncate(($description), 63, array('ending'=>'..', 'html'=>true));?></p>
        </div>
    </div>
    <div class="lesson-box-footer radius2">
        <?php echo $this->Layout->rating($avarageRating); ?>
        <div class="pull-right">
            <div class="price-tag"><span>
                <?php echo $this->Html->link($this->Layout->priceTag($oneOnOnePrice, $fullGroupStudentPrice), array('controller'=>'Order', 'action'=>'init', 'order', $subjectId)); ?>
            </span></div><a href="#"><i class="iconSmall-info space3"></i></a>
        </div>
    </div>
</div>
