<?php echo $this->element('Home/search'); ?>

<?php
if(isSet($subjectsData['breadcrumbs'])) {
    echo $this->element('Home/subject_categories_breadcrumbs', array('subject_categories_breadcrumbs'=>$subjectsData['breadcrumbs'])),'<br /><br />';
}
if(isSet($subjectsData['categories'])) {
    echo $this->element('Home/facet_subject_categories', array('facet_categories'=>$subjectsData['categories'])),'<br />';
}

if(isSet($subjectsData['subjects']) && $subjectsData['subjects']) {
	echo '<p>Found subjects</p>'; 
	foreach($subjectsData['subjects'] AS $subjectData) {
		//pr($subjectData['Subject']);
		echo $this->element('subject_request', $subjectData),'<br />';
	}
}
?>