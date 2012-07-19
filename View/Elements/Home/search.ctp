<div id="search">
	<div class="container">
			
<h2>Search Here or Browse by <a href="#">Categories</a></h2>
<?php
echo $this->Form->create(false, array('url'=>array('controller'=>(isSet($controller) ? $controller : 'Home'), 'action'=>'searchSubject'), 'id'=>'search_form', 'type'=>'get'));

//echo $this->Form->input('lesson_type_video', array('type'=>'checkbox', 'label'=>'video', 'hiddenField'=>false));
//echo $this->Form->input('lesson_type_live', array('type'=>'checkbox', 'label'=>'live', 'hiddenField'=>false));

?>
				<div class="search_box">
					<?php	echo $this->Form->input('search_terms', array('label'=>false));
							echo $this->Form->submit('submit', array('type'=>'image','src' => '/img/search/search.jpg'));
					?>
				</div>
<?php  
echo $this->Form->end();
?>

		</div>
</div>


