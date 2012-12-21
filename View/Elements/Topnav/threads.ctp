<?php
/*
            [thread_id] => 14
            [title] => My second IT support
            [entity_type] => user_lesson
            [entity_id] => 256
            [unread_messages] => 0
            [last_message] => Array
                (
                    [user_id] => 4
                    [message] => dvfgds
                    [timestamp] => 1355079504
                )

            [other_user] => Array
                (
                    [image_source] =>
                    [username] => Sivan Yamin
                )

        )
 */

    foreach($threads AS $thread) {
        echo '<li ',($thread['unread_messages']) ? ' class="visiter-background"' : '','>
                            <a href="#">
                            <div class="headeruser">',
                                $this->Html->image($this->Layout->image($thread['other_user']['image_source'], 38, 38), array('alt' => 'By user image'))
                            ,'</div>
                            <div class="headeruser-text">
                                <p><strong>',$thread['title'],'</strong></p>
                                <p>',$thread['last_message']['message'],'</p>
                            </div>
                            </a>
                        </li>';
    }
?>