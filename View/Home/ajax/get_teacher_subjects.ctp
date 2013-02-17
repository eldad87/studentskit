<?php
if($response['response']['results']) {

    foreach($response['response']['results'] AS $result) {
        echo $this->element('Home'.DS.'other_subject_li', array('teacherSubject'=>$result['Subject'], 'lessonType'=>$result['Subject']['lesson_type']));
    }
}
?>