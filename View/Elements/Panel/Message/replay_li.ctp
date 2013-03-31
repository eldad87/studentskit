<li>
    <div class="msg-user-imgbox"><?php
        if($message['user_id']==$user['user_id']) {
            echo $this->Html->image($this->Layout->image($user['image_source'], 60, 60), array('alt' => 'User image'));
        } else {
            echo $this->Html->image($this->Layout->image($thread['other_user']['image_source'], 60, 60), array('alt' => 'User image'));
        }
        ?></div>
    <div class="msg-textbox">
        <div class="msg-textheaderbox pad8">
            <h5><?php
                $user = $this->getVar('user');
                echo $message['user_id']==$user['user_id'] ? $user['username'] : $thread['other_user']['username'];
                ?></h5>
            <span><?php echo $this->TimeTZ->niceShort($message['timestamp']); ?></span>
        </div>
        <p class="fullwidth"><?php echo $message['message']; ?></p>
        <!-- <p class="msgbottom-text"><a href="#" class="rly">Reply</a><a href="#" class="trash">Trash</a></p>-->
    </div>

    <?php
    if(isSet($message['attachment'])) {
    ?>
        <div class="fullwidth pull-left clearleft space3">
            <?php
            foreach ($message['attachment'] AS $attachment) {?>
                <div class="fullwidth">
                    <?php echo $this->Html->link($attachment['name'], array('controller'=>'FileSystem', 'action'=>'download', $attachment['id']), array('target'=>'_blank')) ?>
                </div>
            <?php
                }
            ?>
        </div>
    <?php
    }
    ?>
</li>