<p class="pull-left bodytop-leftlink">
    <?php
    $urlParams = $this->params['url'];
    unset($urlParams['category_id']);

    echo $this->Html->link('All', array('?'=>$urlParams), true);

    $count = count($subject_categories_breadcrumbs);
    foreach($subject_categories_breadcrumbs AS $subjectCategoryId=>$name) {
        echo '<span>></span>';
        echo $this->Html->link($name, array('?'=>am($urlParams, array('category_id'=>$subjectCategoryId))), array('class'=>(!--$count ? 'color-text' : null))  );
    }

    ?>
</p>