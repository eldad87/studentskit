<div>
<div class="categorybox radius3">
    <h3 class="radius1"><strong><?php echo __('Categories'); ?></strong></h3>
    <ul class="categorystripbox">
        <?php
        foreach($facet_categories AS $category) {
            echo
            '<li>
                <span>(',$category['count'],')</span>
                ',$this->Html->link($category['name'], array('?'=>am($this->params['url'], array('category_id'=>$category['category_id']))), array('class'=>'categorystiptext')),'
            </li>';
        }
        ?>



    <h3 class="radius1 space6"><strong><?php echo __('Filters'); ?></strong></h3>

    <?php echo $this->Form->create(false, array('type'=>'get')); ?>
        <?php echo $this->Form->input('term', array('type'=>'hidden')); ?>

    <p class="category-subheadbar"><?php echo __('Lesson Type'); ?></p>
    <ul class="categorystripbox categorystripbox1">
        <li>
            <div class="categorystiptext">
                <?php echo $this->Form->input(__('Live'), array('type'=>'checkbox')); ?>
            </div>
            <!--<span>()</span>-->
        </li>
        <li>
            <div class="categorystiptext">
                <?php echo $this->Form->input(__('Video'), array('type'=>'checkbox')); ?>
            </div>
            <!--<span>()</span>-->
        </li>
    </ul>

    <p class="category-subheadbar pricesubheader"><?php echo __('Price'); ?></p>
    <ul class="categorystripbox categorystripbox1">
        <li class="nobackground">
            <?php echo $this->Form->input('price_from', array('label'=>false, 'div'=>false)); ?>
            <label id="middletexr"> <?php echo __('to'); ?></label>
            <?php echo $this->Form->input('price_to', array('label'=>false, 'div'=>false)); ?>
            <label id="middletexr1"> $</label>
            <!--<input type="text" value="$" >-->
        </li>
    </ul>

    <p class="category-subheadbar pricesubheader"><?php echo __('Rate'); ?></p>
    <ul class="categorystripbox categorystripbox1">
        <li class="nobackground">
            <?php echo $this->Form->input('average_rating_from', array('label'=>false, 'div'=>false)); ?>
            <label id="middletexr"> <?php echo __('to'); ?></label>
            <?php echo $this->Form->input('average_rating_to', array('label'=>false, 'div'=>false)); ?>
        </li>
    </ul>
    <?php
        echo $this->Form->button(__('Search'), array('class'=>'btn btn-primary pull-right space1'));
        echo $this->Form->end();
    ?>
</div>