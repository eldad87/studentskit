<p class="pull-left bodytop-leftlink">
    <?php
    $urlParams = $this->params['url'];
    unset($urlParams['category_id']);

    echo $this->Html->link('All', array('?'=>$urlParams), true);

    $count = count($categories_breadcrumbs);
    foreach($categories_breadcrumbs AS $categoryId=>$name) {
        echo '<span>></span>';
        echo $this->Html->link($name, array('?'=>am($urlParams, array('category_id'=>$categoryId))), array('class'=>(!--$count ? 'color-text' : null))  );
    }

    ?>
</p>