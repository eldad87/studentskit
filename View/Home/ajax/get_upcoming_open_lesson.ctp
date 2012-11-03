<?php
if($response['response']['results']) {

    foreach($response['response']['results'] AS $upcomingAvailableLesson) {
        echo $this->element('Home/upcoming_lesson_li', array('upcomingAvailableLesson'=>$upcomingAvailableLesson));
    }
}
?>