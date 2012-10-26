<div class="lesson-box">
    <h3 class="radius1"><strong><?php
        //Create a string - max 37 chars, camelize each word -> Abcd eFg -> Abcd Efg
        echo String::truncate($this->Layout->formatTitle($name)); ?></strong></h3>
    <div class="lesson-box-content">
        <div class="user-pic2">
            <?php
            echo $this->Html->image($this->Layout->image($imageSource, 72, 72), array('alt' => 'Topic image'));
            ?>
        </div>
        <div class="usr-text2">
            <p><?php echo String::truncate(($description), 63, array('ending'=>'..', 'html'=>true));?></p>
        </div>
    </div>
    <div class="lesson-box-footer radius2">
        <?php echo $this->Layout->rating($avarageRating); ?>
        <div class="pull-right">
            <div class="price-tag"><span>
                <?php echo $this->Layout->priceTag($oneOnOnePrice, $fullGroupStudentPrice); ?>
            </span></div><a href="#"><i class="iconSmall-info space3"></i></a>
        </div>
    </div>
</div>
<?php
    echo $this->Html->link('Offer', array('controller'=>'Requests', 'action'=>'offerLesson', $subjectId));
?>