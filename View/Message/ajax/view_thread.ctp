<?php
    echo $this->Html->script('jquery.infieldlabel.min');
?>

<script type="text/javascript">
    $(document).ready(function(){
        $("label.infield").inFieldLabels();
        initMenuLinks();

        pfObj.loadForm('#replay-form', '#replay-form', 'post');
        pfObj.setAppendCallback('.cancelThread', 'beforeAjax', function(data){
            //Find textarea, if no value - cancel
            return true;
        });

        pfObj.setAppendCallback('#replay-form', 'before', function(data){
            //Append new message
            $('#messageList').append(data);

            //Clear textarea
            $('#replay').val('');
            $('#replay').blur();

            return false; //So it won't replace the replay form
        });

    });
</script>
<div class="cont-span6 ext-wid cbox-space">
    <div class="fullwidth pull-left">
        <h2 class="pull-left"><?php echo ($response['response']['thread']['title'] ? $response['response']['thread']['title'] : sprintf(__('Conversation with %s'), $response['response']['thread']['other_user']['username'])); ?></h2>
        <div class="pull-right skmsg-headerbtn">
           <a class="btn-blue long-wid2 fontsize1 text-color load2" href="#" rel="<?php echo Router::url(array('controller'=>'Message', 'action'=>'index')); ?>">
               <i class="iconSmall-sidearrow sidearrow" /><?php echo __('Message'); ?>
           </a>
           <!--<a class="btn-blue long-wid2 fontsize1 text-color show-tip" id="action-blue" href="#">
                <i class="iconSmall-small-tool action-icon"></i><span class="actin">Action</span> <i class="iconSmall-drop-arrow action-icon"></i></a>
            <ul class="action-dropdown alltip" id="action-blue-tip" style="display: none; ">
                <li><a href="#">Mark as Unread</a></li>
                <li><a href="#">Forward...</a></li>
                <li class="line"><hr></li>
                <li><a href="#">History</a></li>
                <li><a href="#">Delete Messages</a></li>
                <li><a href="#">Report as spam...</a></li>
                <li><a href="#">Report Conversation...</a></li>
                <li class="line"><hr></li>
                <li><a href="#">Move to other</a></li>
            </ul>-->
        </div>
    </div>
    <div class="fullwidth pull-left">

        <ul class="messagebar" id="messageList">

            <?php
                foreach($response['response']['thread']['messages'] AS $message) {
                    echo $this->element('Panel'.DS.'Message'.DS.'replay_li', array('message'=>$message, 'other_user'=>$response['response']['thread']['other_user']));
                }
            ?>
        </ul>

        <ul class="messagebar">
            <li class="replay">
                <div class="fullwidth pull-left">
                    <div>
                        <form id="replay-form" action="<?php echo Router::url(array('controller'=>'Message', 'action'=>'sendMessage')); ?>">
                            <button id="replayButton" class="btn-blue pull-right">Reply</button>
                            <div class="commentbox-container">
                                <div class="fullwidth">
                                    <div class="infield">
                                    <label class="infield" for="replay"><?php echo __('your message goes here....'); ?></label>
                                    </div>
                                    <textarea id="replay" name="message" class="fullwidth" required="required"></textarea>

                                    <input type="hidden" name="thread_id" value="<?php echo $response['response']['thread']['thread_id']; ?>" />
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