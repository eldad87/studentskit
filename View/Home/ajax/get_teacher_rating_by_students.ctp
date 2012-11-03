<?php
if($response['response']['rating']) {
    foreach($response['response']['rating'] AS $ratingByStudent) {
        echo $this->element('Home/reviews_by_students_div', array('ratingByStudent'=>$ratingByStudent));
    }
}
?>