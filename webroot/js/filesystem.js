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
        _getCurrentFileSystemId: function() {
            var p = $(this).data('path');
            return p.slice(-1)[0];
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
                    //Get settings
                    var downloadSettings = self.fileSystem('_getSetting', 'downloadAction');
                    var downloadUrl = jQuery.nano(downloadSettings['url'], {id: id});
                    window.open( downloadUrl );
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
            var element = '<li id="{file_system_id}" data-type="{type}">' +
                '<i class="iconMedium-folder-{class} pull-left space20"></i>' +
                '<span class="pull-left">{name}</span>' +
                '<div class="pull-right">';
            if(resource['type']=='folder') {
                //Rename is available only for folders
                element +=          '<i class="iconSmall-pencil pencilicon actionButton" id="rename_{file_system_id}"></i>';
            }
            element +=              '<i class="iconSmall-red-cross redcross actionButton" id="delete_{file_system_id}"></i>' +
                '</div>' +
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



            //New folder button
            var newFolderSettings = self.fileSystem('_getSetting', 'newFolderAction');
            $(newFolderSettings['button']).click(function(e) {
                e.stopPropagation();

                var id = self.fileSystem('_getCurrentFileSystemId');
                //Set form url
                $(newFolderSettings['model'] + ' form').attr('action', jQuery.nano(newFolderSettings['url'], {id: id}));

                //Reset element name in popup
                $(newFolderSettings['nameField']).val('');

                //Show popup
                $(newFolderSettings['model']).modal('show');
            });


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