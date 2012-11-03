<?php
if($response['response']['results']) {

    foreach($response['response']['results'] AS $result) {
        echo $this->element('Home/other_subject_li', array('teacherSubject'=>$result['Subject']));
    }
}
?>