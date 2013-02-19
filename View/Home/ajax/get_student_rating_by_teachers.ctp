<?php
if($response['response']['rating']) {
    foreach($response['response']['rating'] AS $ratingByTeacher) {
        echo $this->element('Home'.DS.'reviews_by_teachers_div', array('ratingByTeacher'=>$ratingByTeacher, 'first'=>false));
    }
}
?>