<?php
if($response['response']['results']) {
    $bgColor = 0;

    //Calc last color (even/odd) of the existing records by page and limit.
    $existingRecordsCont = ($page-1)*$limit;
    if($existingRecordsCont%2==0) {
        $bgColor = 1;
    }
    foreach($response['response']['results'] AS $latestTopic) {
        echo $this->element('Home'.DS.'last_board_msg_li', array('latestTopic'=>$latestTopic, 'bgColor'=>++$bgColor));

        if($bgColor==2) {
            $bgColor = 0;
        }
    }
}
?>