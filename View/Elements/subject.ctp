<div class="lesson-box">
    <h3 class="radius1">
        <?php echo $this->Layout->lessonTypeIcon($lessonType); ?>
        <strong><?php
        //Create a string - max 37 chars, camelize each word -> Abcd eFg -> Abcd Efg
        echo String::truncate(
                                $this->Layout->formatTitle($name),
                                array('controller'=>'Home', 'action'=>'teacherSubject', $subjectId),
                                array('class'=>'radius1'));?></strong></h3>
    <div class="lesson-box-content">
        <div class="user-pic2">
            <?php
            echo $this->Html->image($this->Layout->image($imageSource, 72, 72), array('alt' => 'Topic image'));
            ?>
        </div>
        <div class="usr-text2">
            <h4>by <?php echo$this->Layout->formatTitle($teacherUsername); ?></h4>
            <p><?php echo String::truncate(($description), 63, array('ending'=>'..', 'html'=>true));?></p>
        </div>
    </div>
    <div class="lesson-box-footer radius2">
        <?php
         echo $this->Layout->ratingNew($avarageRating, false, 'pull-left space3');
        ?>

        <div class="pull-right">
            <?php
                echo $this->Layout->priceTag($oneOnOnePrice, $fullGroupStudentPrice);
                echo $this->Layout->toolTip($this->Layout->buildLessonTooltipHtml($subjectData));
            ?>

        </div>
    </div>
</div>
