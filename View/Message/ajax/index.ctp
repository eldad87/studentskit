<?php
if($page==1){
?>
<script type="text/javascript">
    $(document).ready(function(){
        //initMenuLinks();

        pAPIObj.loadElement('.cancelThread', 'click', '#mainMessages', 'post');
        pAPIObj.setAppendCallback('.cancelThread', 'after', function(data){
            $('#thread_' + data['response']['thread_id']).hide();
        });

        var url = '/Message/index/{limit}/{page}';
        lmObj.loadMoreButton('#pm-load-more', 'click', '#pm', url, {}, 'get', <?php echo $limit; ?>);
        lmObj.setItemsCountSelector('#pm-load-more', '#pm li' );
    });
</script>
<div class="cont-span15 cbox-space">
    <div class="fullwidth pull-left" id="mainMessages">
        <h2 class="pull-left"><?php echo __('Messages'); ?></h2>
        <!--<div></div>
        <div class="pull-right skmsg-headerbtn skmsg-headerbtn1">
            <a class="btn-blue long-wid2 fontsize1 text-color pull-left msg-blubtn" href="#"><i class="iconSmall-white-add pull-left"></i>
                <p class="newmsg-btntext">New Message</p></a>
            <div class="search-wrapper">
                <div class="searchicon-box"><a href="#"><i class="iconSmall-search-icon"></i></a></div>
                <div class="search-container"><input type="text" value="Search Messages" class="search-box"></div>

            </div>
        </div>-->
        <div class="fullwidth pull-left">
                <ul class="messagebar msgpage" id="pm">
<?php
}

    foreach($response['response']['threads'] AS $thread) {
        $threadLink = Router::url(array('controller'=>'Message', 'action'=>'viewThread', $thread['thread_id']));
?>
        <li id="thread_<?php echo $thread['thread_id']; ?>" <?php echo ( $thread['unread_messages'] ? ' class="msg-active a-black"' : 'class="a-black"'); ?>>

            <a class="load2" href="#" rel="<?php echo $threadLink; ?>">
                <div class="msg-user-imgbox">
                    <?php echo $this->Html->image($this->Layout->image($thread['other_user']['image_source'], 60, 60), array('alt' => 'User image')) ?>
                </div>
            </a>
            <div class="msg-textbox">
                <a class="load2" href="#" rel="<?php echo $threadLink; ?>">
                    <div class="msg-textheaderbox pad8">
                        <h5><?php echo ($thread['title'] ? $thread['title'] : sprintf(__('Conversation with %s'), $thread['other_user']['username'])); ?></h5>
                        <span><?php echo $this->TimeTZ->niceShort($thread['last_message']['timestamp']); ?></span>
                    </div>
                    <p class="fullwidth"><?php echo $thread['last_message']['message']; ?></p>
                </a>


                <p class="msgbottom-text"><a href="#" data-target="<?php echo Router::url(array('controller'=>'Message', 'action'=>'deleteThread', $thread['thread_id']));
                    ?>" class="cancelThread"><i class="iconSmall-red-cross"></i></a></p>
            </div>
        </li>
<?php
    }
if($page==1) {
?>


                </ul>

        </div>

    <?php
    if(count($response['response']['threads'])>=$limit) {
        echo '<a href="#" class="more radius3 gradient2 space9" id="pm-load-more"><strong>', __('Load More') ,'</strong><i class="iconSmall-more-arrow"></i></a>';
    }
    ?>
    </div>
</div>
<?php
}

    /**/