<?php
    foreach($notifications AS $notification) {
        echo '<li', ($notification['unread'] ? ' class="visiter-background"' : '') ,' data-notification-id="',$notification['notification_id'],'">
                <a href="#">
                    <div class="headeruser">',$this->Html->image($this->Layout->image($notification['by_user_image_source'], 38, 38), array('alt' => 'By user image')),'</div>
                    <div class="headeruser-text">
                        <p>',$notification['message'],'</p>
                    </div>
                </a>
            </li>';
    }
?>