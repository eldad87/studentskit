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

                }

                return true;
            });


        //Bind trigger that will be fired right when fileSystem will be loaded
        $('#file-list').on('pathChange', function(e, data){ //When path changes, apply the changes to the fineUploader

            //Set the new path, will be used for uploading new files
            $('#bootstrapped-fine-uploader').fineUploader('setParams', data);

            //Remove all upload status records
            $('#uploadList').find('li.alert-success').remove();
        });

        //init FS
        var fs = jQuery.parseJSON('<?php echo json_encode($fileSystem) ?>');
        $('#file-list').fileSystem( fs, {
            pathDisplay: $('#pathDisplay'),
            goUpInPath: $('#goUpInPath'),
            newFolderAction: {button: '#new-folder',model: '#rename-popup', nameField: '#newName', url: '<?php echo Router::url(array('controller'=>'FileSystem', 'action'=>'addFolder', '{id}')); ?>'},
            renameAction: {model: '#rename-popup', nameField: '#newName', url: '<?php echo Router::url(array('controller'=>'FileSystem', 'action'=>'rename', '{id}')); ?>'},
            deleteAction: {url: '<?php echo Router::url(array('controller'=>'FileSystem', 'action'=>'delete', '{id}')); ?>', errorElement: '#errorElement'},
            downloadAction: {url: '<?php echo Router::url(array('controller'=>'FileSystem', 'action'=>'download', '{id}')); ?>'}
        });

        //if(jQuery.isFunction('initNextButton') ) {
            initNextButton('<?php echo Router::url(array('controller'=>'Teacher', 'action'=>'setSubjectCreationStage', '{subject_id}', '{creation_stage}')) ?>');
        //}
    });


</script>

<div class="fullwidth pull-left">
    <div class="form-first">
        <form class="sk-form">
            <fieldset>
                <div class="control-group">
                    <div class="control m-none-left ">
                        <p class="x-large8 advicebar">
                            The files will be available for download.
                        </p>
                    </div>
                </div>

                <div class="control-group">
                    <div class="control m-none-left">
                        <input type="text" class="x-large8" name="" value="/" id="pathDisplay" disabled="disabled" />
                    </div>
                </div>
                <div class="list-box-file radius3">
                    <div class="upr-file-box" id="goUpInPath">
                        <i class="iconMedium-arrow-turn pull-left"></i>
                        <p class="pull-left">...</p>
                    </div>
                    <div id="errorElement" class="pull-left fullwidth"></div>
                    <ul class="file-box-all" id="file-list"></ul>
                    <div id="bootstrapped-fine-uploader"></div>
                </div>
            </fieldset>
        </form>
    </div> <!-- /form-first -->

    <div class="spsbjectfile-selector">
        <a id="new-folder" href="#" class="fullwidth pull-left spsbjectfile-selector">
            <i class="iconMedium-add-sub"></i>
            <span><?php echo __('Create folder'); ?></span>
        </a>

        <div id="fileUpload" href="#" class="fullwidth pull-left spsbjectfile-selector fileUpload">
            <i class="iconMedium-upload"></i>
            <span><?php echo __('Upload file'); ?></span>
        </div>
    </div>
</div>
    <?php
        if( $creationStage && $creationStage < CREATION_STAGE_FILES ) {
    ?>
            <div class="cont-span6 cbox-space">
                <div class="control-group control2">
                    <label class="control-label"></label>
                    <div class="control">
                        <button class="btn-blue pull-right nextButton" data-creation-stage="<?php echo CREATION_STAGE_FILES; ?>" data-subject-id="<?php echo $subjectId; ?>" type="Save"><?php echo __('Next'); ?></button>
                    </div>
                </div>
            </div>
    <?php
        }
    ?>
</div>



<script type="text/javascript">
    pAPIObj.loadElement('#rename-form', 'submit', '#rename-popup .modal-body', 'post');
    pAPIObj.setAppendCallback('#rename-form', 'after', function(data){
        if(data['response']['title'][0]=='Success') {

            $('#file-list').fileSystem('updateResource', data['response']['results'] );

            //Close popup
            $('#rename-popup').modal('hide');
        }
    });

</script>

<div id="rename-popup" class="modal hide fade myModal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Rename file</h3>
    </div> <!-- /modal-header -->
    <form class="sk-form" id="rename-form" method="post" action="">
        <div class="modal-body">
            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="newName">Name :</label>
                    <div class="control control1">
                        <input type="text" class="x-large2" name="FileSystem[name]" value="" id="newName">
                    </div>
                </div>
            </fieldset>
        </div> <!-- /modal-body -->
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('No'); ?></button>
            <button class="btn btn-primary" id="save-rename"><?php echo __('Save'); ?></button>
        </div>
    </form>
</div>