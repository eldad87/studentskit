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


            //Remove all current attachments
            $('#attachments input').each(function() {
                $('#file-list').fileSystem('removeResource', $(this).val());
            });

            //Bug fix the height of listed attachments
            $('#bootstrapped-fine-uploader .qq-upload-list').height('0px')
            $('#bootstrapped-fine-uploader .qq-upload-list').height('auto')

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

                                    <div id="attachments"></div>
                                </div>
                                <div class="fullwidth space23">
                                    <div id="fileUpload" class="pull-left">
                                        <i class="iconSmall-clip pull-left"></i>
                                    </div>
                                    <!--<a href="#"><i class="iconSmall-camera pull-left space27"></i></a>
                                    <i class="iconSmall-enter-arrow pull-right space27"></i>
                                    <input type="checkbox" name="enter" class="pull-right">-->
                                </div>

                                <!-- Attachments -->
                                <div class="fullwidth space23 pull-left">
                                    <script type="text/javascript">
                                        $(document).ready(function(){


                                            $('#bootstrapped-fine-uploader').fineUploader({
                                                button: $('#fileUpload'),
                                                request: {
                                                    endpoint: '<?php echo Router::url(array('controller'=>'FileSystem', 'action'=>'uploadFile')); ?>',
                                                    //endpoint: '/Upload/ajax_upload',
                                                    inputName: 'fileUpload'
                                                },
                                                template:   '<div class="qq-uploader span12 pull-left">' +
                                                            '<pre class="qq-upload-drop-area span12 qq-uploader span12"><span>{dragZoneText}</span></pre>' +
                                                            '<ul class="qq-upload-list" id="uploadList" style="margin-top: 10px; text-align: center;"></ul>' +
                                                            '</div>',
                                                classes: {
                                                    success: 'alert alert-success',
                                                    fail: 'alert alert-error'
                                                },
                                                debug: true

                                            //Add events
                                            }).on('complete', function(event, id, filename, reason) {
                                                if(event.type='complete') {
                                                    //1. remove the success files indication.
                                                    $('#uploadList').find('li.alert-success').remove();

                                                    //2. add new resource
                                                    $('#file-list').fileSystem( 'addResource', {path: reason['data']['path'],
                                                        resource: {
                                                            file_system_id: reason['data']['file_system_id'],
                                                            type:           'file',
                                                            name:           reason['data']['name'],
                                                            size_kb:        reason['data']['size'],
                                                            extension:      reason['data']['ext']
                                                        }
                                                    });

                                                    //3. Add new file as hidden field
                                                    $('#attachments').append('<input type="hidden" name="attachment[]" id="attachment_' + reason['data']['file_system_id'] + '" value="' + reason['data']['file_system_id'] + '" />');
                                                }

                                                return true;
                                            });

                                            //Delete attachment from form
                                            $('#file-list').on('removeResource', function(e, data){
                                                $('#attachment_' + data.id).remove();
                                            });

                                            //Bind trigger that will be fired right when fileSystem will be loaded
                                            $('#file-list').on('pathChange', function(e, data){ //When path changes, apply the changes to the fineUploader

                                                //Show/Hide upload/create folder button
                                                delete data['parent'];

                                                //Set the new path, will be used for uploading new files
                                                $('#bootstrapped-fine-uploader').fineUploader('setParams', data);
                                            });

                                            //init FS - just use the root_file_system_id (we don't want to show existing files yet)
                                            var fs =  jQuery.parseJSON('<?php echo json_encode($fs) ?>');;
                                            $('#file-list').fileSystem( fs, {
                                                deleteAction: {url: '<?php echo Router::url(array('controller'=>'FileSystem', 'action'=>'delete', '{id}')); ?>', errorElement: '#errorElement'},
                                                downloadAction: {url: '<?php echo Router::url(array('controller'=>'FileSystem', 'action'=>'download', '{id}')); ?>'},
                                                resourcePermissions: function(parent, resource) {
                                                    return {
                                                        'delete': true
                                                    };
                                                }

                                            });
                                        });
                                    </script>

                                    <div id="errorElement" class="pull-left fullwidth"></div>
                                    <ul class="file-box-all" id="file-list"></ul>
                                    <div id="bootstrapped-fine-uploader"></div>
                                </div>
                                <!-- /Attachments -->



                            </div>
                        </form>
                    </div>
                </div>
            </li>
        </ul>

    </div> <!-- /fullwidth -->
</div>