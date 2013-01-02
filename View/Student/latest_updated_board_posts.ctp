<?php
$page--;
$num = $page*$limit;
foreach($latestUpdatedTopics AS $topic) {
    $num++;
    echo $this->element('Panel/latest_board_msg_index_li', array('topic'=>$topic, 'num'=>$num));
}
?>