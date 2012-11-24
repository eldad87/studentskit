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
            _clear: function() {
                $(this).find('li').remove();

                return $(this);
            },

            _bindBehavior: function(id) {
                var self = $(this);


                //Find element
                var element = $('#' + id);

                element.click(function() {

                    //Get data
                    var elementData = $(this).data();

                    if(elementData['type']=='folder') {
                        self.fileSystem('show', id);
                        //File, download it
                    } else {
//TODO:
                    }

                });

                //Buttons Rename/Delete
                //Click - download files, open path folder
            },


            _drawResource: function(resource) {
                resource['class'] = (resource['type']=='folder' ? 'norm' : 'black');

                //Build element
                var element = '<li id="{file_system_id}" data-type="{type}"><i class="iconMedium-folder-{class} pull-left space20"></i><span class="pull-left">{name}</span>' +
                    '<div class="pull-right"><i class="iconSmall-pencil pencilicon actionButton" id="id="rename_{file_system_id}"></i><i class="iconSmall-red-cross redcross actionButton" id="delete_id="{file_system_id}"></i></div>' +
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
                resources[resource['file_system_id']] = resource;


                //Clear existing records
                $(this).fileSystem('_clear');

                //re-render current view
                $(this).fileSystem('_showResources', $(this).data('path') );


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
                                self.fileSystem('show', '..');
                            });
                        }
                    }

                //});
            },

            /**
             * show all elements in path
             * @param path undefined - show root, '..' - show upper level, 'string' - go deeper
             */
            show : function( path ) {

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

                //Clear existing records
                $(this).fileSystem('_clear');


                //Trigger an event - that the path is about to change
                $(this).trigger('pathChange', {path: $(this).data('path')});

                //show records
                $(this).fileSystem('_showResources', $(this).data('path') );

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

        };

    })( jQuery );





    $(document).ready(function(){





        $('#bootstrapped-fine-uploader').fineUploader({
            button: $('#fileUpload'),
            request: {
                endpoint: '/Upload/ajax_upload'
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

        //init FS
        var fs = jQuery.parseJSON('<?php echo json_encode($fileSystem) ?>');
        $('#file-list').fileSystem( fs, {
            pathDisplay: $('#pathDisplay'),
            goUpInPath: $('#goUpInPath'),
            renameAction: 'url',
            deleteAction: 'url',
            downloadAction: 'url'
        } );

        $('#file-list').fileSystem( 'show' )
            //When path changes, apply the changes to the fineUploader
            .on('pathChange', function(e, data){
                $('#bootstrapped-fine-uploader').fineUploader('setParams', data);
            });
    });
</script>

<?php

?>
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
                        <input type="text" class="x-large8" name="" value="/" id="pathDisplay">

                        <div class="modal hide fade myModal1">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">×</button>
                                <h3>Folder name</h3>
                            </div> <!-- /modal-header -->
                            <div class="modal-body">

                                <div class="control-group">
                                    <label class="control-label" for="11">Name :</label>
                                    <div class="control control1">
                                        <input type="text" class="x-large2" name="" value="" id="12">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label"></label>
                                    <div class="control  control1 pull-right">
                                        <button class="btn-blue" type="button">Save</button>
                                    </div>
                                </div>

                            </div> <!-- /modal-body -->
                        </div>
                    </div>
                </div>
                <div class="list-box-file radius3">
                    <div class="upr-file-box" id="goUpInPath">
                        <i class="iconMedium-arrow-turn pull-left"></i>
                        <p class="pull-left">...</p>
                    </div>

                    <ul class="file-box-all" id="file-list">


                    </ul>
                    <div id="bootstrapped-fine-uploader"></div>
                </div>
            </fieldset>
        </form>
    </div> <!-- /form-first -->

    <div class="spsbjectfile-selector">
        <a data-toggle="modal" href="#myModal" class="fullwidth pull-left spsbjectfile-selector">
            <i class="iconMedium-add-sub"></i>
            <span><?php echo __('Create folder'); ?></span>
        </a>



        <a id="fileUpload" href="#" class="fullwidth pull-left spsbjectfile-selector fileUpload">
            <i class="iconMedium-upload"></i>
            <span><?php echo __('Upload file'); ?></span>
        </a>
    </div>

    <!--<div id='docUploadContainer'>
        <div id='docUploadButton' class="qq-upload-button btn btn-success">Upload</div>
    </div>-->
</div>





<div id="myModal" class="modal hide fade myModal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Rename file</h3>
    </div> <!-- /modal-header -->
    <div class="modal-body">
        <form class="sk-form">
            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="11">Name :</label>
                    <div class="control control1">
                        <input type="text" class="x-large2" name="" value="" id="11">
                    </div>
                </div>

            </fieldset>
        </form>
    </div> <!-- /modal-body -->
</div>