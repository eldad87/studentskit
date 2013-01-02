<script type="text/javascript">
    $(document).ready(function(){
        /* My Subject */

        //Scroll
        $('.message-tm-stu').slimScroll({
            height: '240px',
            start: 'top',
            width: '100%',
            disableFadeOut: true
        });
        var url = '/Student/latestUpdatedBoardPosts/{limit}/{page}';
        lmObj.loadMoreButton('a.message-tm-more', 'click', 'ul.message-tm-stu', url, jsSettings, 'get', 3);
    });
</script>

<h5 class="space2"><strong><?php echo __('Board Messages'); ?></strong></h5>
<div class="pull-left space8 fullwidth">

    <ul class="message-tm-stu a-black">
    <?php
        $num = 0;
        foreach($latestUpdatedTopics AS $topic) {
            $num++;

            echo $this->element('Panel/latest_board_msg_index_li', array('topic'=>$topic, 'num'=>$num));
        }
    ?>
    </ul>
    <a href="#" class="message-tm-more fontsize1">More Threads</a>
</div>