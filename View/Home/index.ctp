<?php echo $this->element('Home/search');  ?>

<div id="subjects" class="container">
<?php
if($newSubjects) {
	echo '<p>Newest subjects</p>'; 
	foreach($newSubjects AS $newSubject) {
		$newSubject['Subject']['one_on_one_price'] = $newSubject['Subject']['1_on_1_price'];
		echo $this->element('subject', $newSubject['Subject']);
	}
}
?>
</div>


<h3 style="float:left; clear:both">Latest topics</h3>
    <div style="float:left; clear:both">
<?php
foreach($latestTopics AS $topic) {
    echo 'date: '.$topic['Topic']['modified'],'<br />';
    echo 'title: '.$topic['Topic']['title'],'<br />';
    echo 'content: '.$topic['LastPost']['content'],'<br />';
    echo 'User: '.$topic['User']['username'],'<br />';
    echo 'Image: '.$topic['User']['image'],'<br /><br />';
}
?>
        </div>