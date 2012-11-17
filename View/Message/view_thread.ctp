<?php
    echo $this->Html->script('jquery.infieldlabel.min');
    echo $this->Html->scriptBlock('
    $(document).ready(function(){
        $("label.infield").inFieldLabels();
    });
    ');

//pr($thread);

?>
<div class="cont-span6 ext-wid cbox-space">
    <div class="fullwidth pull-left">
        <h2 class="pull-left"><?php echo ($thread['title'] ? $thread['title'] : sprintf(__('Conversation with %s'), $thread['other_user']['username'])); ?></h2>
        <!--<div class="pull-right skmsg-headerbtn">
            <a class="btn-blue long-wid2 fontsize1 text-color" href="#"><i class="iconSmall-sidearrow sidearrow"></i>Message</a>
            <a class="btn-blue long-wid2 fontsize1 text-color show-tip" id="action-blue" href="#">
                <i class="iconSmall-small-tool action-icon"></i><span class="actin">Action</span> <i class="iconSmall-drop-arrow action-icon"></i></a>
            <ul class="action-dropdown alltip" id="action-blue-tip" style="display: none; ">
                <li><a href="#">Mark as Unread</a></li>
                <li><a href="#">Forward...</a></li>
                <li class="line"><hr></li>
                <li><a href="#">Archive</a></li>
                <li><a href="#">Delete Messages</a></li>
                <li><a href="#">Report as spam...</a></li>
                <li><a href="#">Report Conversation...</a></li>
                <li class="line"><hr></li>
                <li><a href="#">Move to other</a></li>
            </ul>
        </div>-->
    </div>
    <div class="fullwidth pull-left">

        <ul class="messagebar">

            <?php
                foreach($thread['messages'] AS $message) {
            ?>
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
                                echo $message['user_id']==$user['user_id'] ? $user['username'] : $thread['other_user']['username'];
                                ?></h5>
                            <span><?php echo $this->Time->niceShort($message['timestamp']); ?></span>
                        </div>
                        <p class="fullwidth"><?php echo $message['message']; ?></p>
                       <!-- <p class="msgbottom-text"><a href="#" class="rly">Reply</a><a href="#" class="trash">Trash</a></p>-->
                    </div>
                </li>

            <?php
                }
            ?>
        </ul>

        <ul class="messagebar">
            <li class="replay">
                <div class="fullwidth pull-left">
                    <div>
                        <form>
                            <button class="btn-blue pull-right">Reply</button>
                            <div class="commentbox-container">
                                <div class="fullwidth">
                                    <div class="infield">
                                    <label class="infield" for="replay"><?php echo __('your message goes here....'); ?></label>
                                    </div>
                                    <textarea id="replay" class="fullwidth" required="required"></textarea>
                                </div>
                                <div class="fullwidth space23">
                                    <!--<a href="#"><i class="iconSmall-clip pull-left"></i></a>
                                    <a href="#"><i class="iconSmall-camera pull-left space27"></i></a>
                                    <i class="iconSmall-enter-arrow pull-right space27"></i>
                                    <input type="checkbox" name="enter" class="pull-right">-->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </li>
        </ul>

    </div> <!-- /fullwidth -->
</div>