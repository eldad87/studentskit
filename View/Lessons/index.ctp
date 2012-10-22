<?php
echo 'is_teacher: ', var_dump($is_teacher),'<br />';
echo 'datetime: ', $datetime,'<br />';
echo '<br />';

if(isSet($meetingId) && isSet($meetingSettings)) {
    echo $this->Watchitoo->initJS($meetingId, $meetingSettings);
    echo $this->Watchitoo->embedMeetingJS($meetingId, $meetingSettings);


    echo '<br />';
    if(isSet($fileSystem)) { echo 'fileSystem: ', var_dump($fileSystem),'<br />'; }
    if(isSet($tests)) { echo 'tests: ', var_dump($tests),'<br />'; }
} else {
    echo 'counter';
}
?>
