<div class="cont-span15 cbox-space">
    <div class="fullwidth pull-left">
        <h2 class="pull-left">Messages</h2>
        <!--<div class="pull-right skmsg-headerbtn skmsg-headerbtn1">
            <a class="btn-blue long-wid2 fontsize1 text-color pull-left msg-blubtn" href="#"><i class="iconSmall-white-add pull-left"></i>
                <p class="newmsg-btntext">New Message</p></a>
            <div class="search-wrapper">
                <div class="searchicon-box"><a href="#"><i class="iconSmall-search-icon"></i></a></div>
                <div class="search-container"><input type="text" value="Search Messages" class="search-box"></div>

            </div>
        </div>-->
        <div class="fullwidth pull-left">
                <ul class="messagebar msgpage">
<?php
    foreach($threads AS $thread) {
?>
        <li <?php echo ( $thread['unread_messages'] ? ' class="msg-active"' : null); ?>>
            <div class="msg-user-imgbox"><?php

                echo $this->Html->image($this->Layout->image($thread['other_user']['image_source'], 60, 60), array('alt' => 'User image'))
                ?></div>
            <div class="msg-textbox">
                <div class="msg-textheaderbox pad8">
                    <h5><?php echo ($thread['title'] ? $thread['title'] : sprintf(__('Conversation with %s'), $thread['other_user']['username'])); ?></h5>
                    <span><?php echo $this->Time->niceShort($thread['last_message']['timestamp']); ?> <!--<a href="#"><i class="iconSmall-dotted-pencil"></i></a>--></span>
                </div>
                <p class="fullwidth"><?php echo $thread['last_message']['message']; ?></p>
                <p class="msgbottom-text"><a href="#"><i class="iconSmall-red-cross"></i></a></p>
            </div>
        </li>
<?php
    }
?>


                </ul>
            <a href="#" class="more radius3 gradient2 space9 pull-left"><strong>Load More</strong><i class="iconSmall-more-arrow"></i></a>
        </div>
    </div>
</div>