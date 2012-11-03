<?php
if($response['response']['lessons']) {
    foreach($response['response']['lessons'] AS $archiveLesson) {
        echo $this->element('Home/latest_lessons_div', array('archiveLesson'=>$archiveLesson, 'first'=>false));
    }
}
?>