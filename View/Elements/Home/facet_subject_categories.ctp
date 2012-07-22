<div id="categories">
			
<h2>Categories</h2>
<?php
    foreach($facet_categories AS $subjectCategory) {
        echo $this->Html->link($subjectCategory['name'].' ('.$subjectCategory['count'].')', array('?'=>am($this->params['url'], array('category_id'=>$subjectCategory['subject_category_id']))), true),'<br />';
    }
?>

</div>


