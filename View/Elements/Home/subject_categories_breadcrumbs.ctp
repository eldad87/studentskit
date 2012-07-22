<div id="categories_breadcrumbs">
			
<h2>Breadcrumbs</h2>
<?php
    $urlParams = $this->params['url'];
    unset($urlParams['category_id']);
    echo $this->Html->link('all', array('?'=>$urlParams), true),' / ';

    foreach($subject_categories_breadcrumbs AS $subjectCategoryId=>$name) {
        echo $this->Html->link($name, array('?'=>am($urlParams, array('category_id'=>$subjectCategoryId))), true),' / ';
    }
?>

</div>


