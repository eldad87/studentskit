<?php
echo 'is_teacher: ', var_dump($is_teacher),'<br />';
echo 'datetime: ', $datetime,'<br />';
if(isSet($meeting)) {
    echo 'meeting: ', $meeting,'<br />';
} else {
    echo 'counter<br />';
}
?>
