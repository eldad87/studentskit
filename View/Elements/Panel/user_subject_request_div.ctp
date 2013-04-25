<div class="lesson-box space2" id="wish_list_id_<?php echo $wishData['WishList']['wish_list_id']; ?>">
    <div class="head-back radius1">
        <h1><?php echo $this->Layout->lessonTypeIcon($wishData['WishList']['lesson_type']); ?><strong><?php echo $wishData['WishList']['name']; ?></strong></h1>
        <div class="dropdown pull-right">
            <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" href="#">
                <i class="iconSmall-drop-arrow"></i>
            </a>
            <ul class="dropdown-menu popupcontent-box" role="menu" aria-labelledby="dLabel">
                <li><a href="#" class="lesson-request"  data-wish_list_id="<?php echo $wishData['WishList']['wish_list_id']; ?>"
                       data-replace-with="wish_list_id_<?php echo $wishData['WishList']['wish_list_id']; ?>"
                       data-append-template="user-panel"><?php echo __('Edit'); ?></a></li>
                <li><a href="#" class="confirm-delete" data-cancel-prefix="wish_list_id" data-id="<?php echo $wishData['WishList']['wish_list_id']; ?>"><?php echo __('Cancel'); ?></a></li>
            </ul>
        </div>

    </div>
    <div class="lesson-box-content">
        <div class="user-pic2"><?php echo $this->Html->image($this->Layout->image($wishData['WishList']['image_source'], 72, 72), array('alt' => 'Lesson image')); ?></div>
        <div class="usr-text2">
            <p><?php echo $wishData['WishList']['description']; ?></p>
        </div>
    </div>
    <div class="lesson-box-footer radius2">

        <div class="pull-right space21 right-i-mar">
            <?php
                echo $this->Layout->toolTip($this->Layout->buildLessonTooltipHtml($wishData['WishList']), null, 'pull-right space23', 'tooltip_'.$wishData['WishList']['wish_list_id']);
                echo $this->Layout->priceTag($wishData['WishList']['1_on_1_price'], null, 'price-tag-panel');
            ?>
        </div>
    </div> <!-- /lesson-box-footer -->
</div> <!-- /lesson-box -->