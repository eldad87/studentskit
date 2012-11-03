<?php
echo $this->Html->link(
    '<li class="bg-color'.$bgColor.'">
        <div class="user-pic1">'.$this->Html->image($this->Layout->image($latestTopic['User']['image_source'], 60, 60), array('alt' => 'User image')).'</div>
        <div class="usr-text1">
            <h6>'.$latestTopic['Topic']['title'].'</h6>
            <p>'.$latestTopic['LastPost']['content'].'</p>
        </div>
    </li>', array('plugin'=>'forum','controller'=>'topics', 'action'=>'view', $latestTopic['Topic']['slug']), array('escape'=>false));
?>