<style>
        /* Fine Uploader
       -------------------------------------------------- */
    .qq-upload-list {
        text-align: left;
    }

        /* For the bootstrapped demos */
    li.alert-success {
        background-color: #DFF0D8;
    }

    li.alert-error {
        background-color: #F2DEDE;
    }

    .alert-error .qq-upload-failed-text {
        display: inline;
    }
</style>

<script type="text/javascript">

    (function( $ ){
        var methods = {
            _getSetting: function(name) {
                var settings = $(this).data('settings');
                return settings[name];
            },
            _clear: function() {
                $(this).find('li').remove();
                return $(this);
            },
            _getResourcesInPath: function(path) {
                if(!path) {
                    //Set current view path
                    path = $(this).data('path')
                }

                var resources = $(this).data('fileSystem');

                for(var i in path) {
                    if(!resources[path[i]]) {
                        resources[path[i]] = {};
                    }

                    if(!resources[path[i]]['children']) {
                        resources[path[i]]['children'] = {};
                    }

                    resources = resources[path[i]]['children'];
                }

                return resources;
            },

            _bindBehavior: function(id) {
                var self = $(this);


                //Find element
                var element = $('#' + id);
                element.click(function() {
                    //Get data
                    var elementData = $(this).data();

                    //Folder, open it
                    if(elementData['type']=='folder') {
                        self.fileSystem('nav', id);

                    //File, download it
                    } else {
//TODO: onClick - download files, open path folder
                    }
                });


                //Rename popup
                var nameOfElement = element.find('span').html(); //Find element name
                $('#rename_' + id).click(function(e) {
                    e.stopPropagation();

                    //Get settings
                    var renameSettings = self.fileSystem('_getSetting', 'renameAction');
                    //Set form url
                    $(renameSettings['model'] + ' form').attr('action', jQuery.nano(renameSettings['url'], {id: id}));

                    //Set element name in popup
                    $(renameSettings['nameField']).val(nameOfElement);
                    //Show popup
                    $(renameSettings['model']).modal('show');
                });

                //Delete popup
                $('#delete_' + id).click(function(e) {
                    e.stopPropagation();

                    //Get settings
                    var deleteSettings = self.fileSystem('_getSetting', 'deleteAction');

                    //Server call to remove this object
                    $.ajax({
                        url: jQuery.nano(deleteSettings['url'], {id: id}),
                        type: 'post',
                        dataType: 'json'
                    }).done(function ( data ) {
                        if(data['response']['title'][0]=='Success') {
                            //remove old error message
                            showError(deleteSettings['errorElement']);
                            self.fileSystem('removeResource', id);
                        } else {
                            //Set new error message
                            showError(deleteSettings['errorElement'], data['response']['description'][0], '');
                        }
                    });

                });


            },

            _drawResource: function(resource) {
                resource['class'] = (resource['type']=='folder' ? 'norm' : 'black');

                //Build element
                var element = '<li id="{file_system_id}" data-type="{type}"><i class="iconMedium-folder-{class} pull-left space20"></i><span class="pull-left">{name}</span>' +
                    '<div class="pull-right"><i class="iconSmall-pencil pencilicon actionButton" id="rename_{file_system_id}"></i>' +
                    '<i class="iconSmall-red-cross redcross actionButton" id="delete_{file_system_id}"></i></div>' +
                    '</li>';
                element = jQuery.nano(element, resource);

                //Append element
                $(this).append(element);

                //Bind behavior/events
                $(this).fileSystem('_bindBehavior', resource['file_system_id']);

                return $(this);
            },

            _showResources: function(path) {

                //1. Find the right resources matching path
                var resources = $(this).data('fileSystem');


                //2. build path display
                var pathDisplay = '/';
                for(var i in path) {
                    pathDisplay += resources[path[i]]['name'] + '/';
                    resources = resources[path[i]]['children'];
                }


                //Render path
                var settings = $(this).data('settings');
                if(settings['pathDisplay']) {
                    settings['pathDisplay'].val(pathDisplay);
                }


                //Render resources
                for(var i in resources) {
                    $(this).fileSystem('_drawResource', resources[i]);
                }

                return $(this);
            },

            addResource: function(data) {

                var path = data['path'];
                var resource = data['resource'];

                //Add resource to tree
                var resources = $(this).fileSystem('_getResourcesInPath', path);
                resources[resource['file_system_id']] = resource;

                $(this).fileSystem('render');

                return $(this);
            },

            /**
             * Updates apply in the current path, unless told otherwise
             * @param data
             */
            updateResource: function( resource, path ) {
                //Update resource on tree`
                var resources = $(this).fileSystem('_getResourcesInPath', path);
                resources[resource['file_system_id']] = $.extend(resources[resource['file_system_id']],  resource);

                $(this).fileSystem('render');
                return $(this);
            },


            removeResource: function(fileSystemId, path) {
                var resources = $(this).fileSystem('_getResourcesInPath', path);
                delete resources[fileSystemId];

                $(this).fileSystem('render');
                return $(this);
            },

            render: function() {
                //Clear existing records
                $(this).fileSystem('_clear');

                //re-render current view
                $(this).fileSystem('_showResources', $(this).data('path') );
            },


            /**
             * show all elements in path
             * @param path undefined - show root, '..' - show upper level, 'string' - go deeper
             */
            nav : function( path ) {

                //Show root
                if(path==undefined) {
                    $(this).data('path', []);

                    //Show upper level
                } else if(path=='..') {

                    var p = $(this).data('path');
                    p.pop();
                    $(this).data('path', p);

                    //Go deeper
                } else {
                    var p = $(this).data('path');
                    p.push(path);
                    $(this).data('path', p);

                }

                $(this).trigger('pathChange', {path: $(this).data('path')} );

                $(this).fileSystem('render');

                return $(this);
            },

            init : function( resources, settings ) {
                //return this.each(function(){

                    var $this = $(this),
                        data = $this.data('fileSystem');


                    // If the plugin hasn't been initialized yet
                    if ( ! data ) {
                        $(this).data('fileSystem', resources);
                        $(this).data('settings', settings);

                        if(settings['goUpInPath']) {
                            var self = $(this);
                            settings['goUpInPath'].click(function(){
                                self.fileSystem('nav', '..');
                            });
                        }
                    }

                //});

                //Set global events
                $(this).fileSystem('nav');

                return $(this);
            }

        };

        $.fn.fileSystem = function( method ) {

            // Method calling logic
            if ( methods[method] ) {
                return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, arguments );
            } else {
                $.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
            }

            return this;
        };

    })( jQuery );


    $(document).ready(function(){
        //init FS
        var fs = jQuery.parseJSON('<?php echo json_encode($fileSystem) ?>');
        $('#file-list').fileSystem( fs, {
            pathDisplay: $('#pathDisplay'),
            goUpInPath: $('#goUpInPath'),
            renameAction: {model: '#rename-popup', nameField: '#newName', url: '<?php echo Router::url(array('controller'=>'Teacher', 'action'=>'FSRename', '{id}')); ?>'},
            deleteAction: {url: '<?php echo Router::url(array('controller'=>'Teacher', 'action'=>'FSDelete', '{id}')); ?>', errorElement: '#errorElement'},
            downloadAction: 'url'


        //When path changes, apply the changes to the fineUploader
        }).on('pathChange', function(e, data){
                //Set the new path, will be used for uploading new files
                $('#bootstrapped-fine-uploader').fineUploader('setParams', data);

                //Remove all upload status records
                $('#uploadList').find('li.alert-success').remove();
            });


        $('#bootstrapped-fine-uploader').fineUploader({
            button: $('#fileUpload'),
            request: {
                endpoint: '<?php echo Router::url(array('controller'=>'Teacher', 'action'=>'uploadSubjectFile', $subjectId)); ?>',
                //endpoint: '/Upload/ajax_upload',
                inputName: 'fileUpload'
            },
            template:   '<div class="qq-uploader span12 pull-left">' +
                            '<pre class="qq-upload-drop-area span12 qq-uploader span12"><span>{dragZoneText}</span></pre>' +
                            //'<div class="qq-upload-button btn btn-success" style="width: auto;">{uploadButtonText}</div>' +
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
                        <input type="text" class="x-large8" name="" value="/" id="pathDisplay" />
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
        <a data-toggle="modal" href="#rename-form" class="fullwidth pull-left spsbjectfile-selector">
            <i class="iconMedium-add-sub"></i>
            <span><?php echo __('Create folder'); ?></span>
        </a>

        <a id="fileUpload" href="#" class="fullwidth pull-left spsbjectfile-selector fileUpload">
            <i class="iconMedium-upload"></i>
            <span><?php echo __('Upload file'); ?></span>
        </a>
    </div>
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