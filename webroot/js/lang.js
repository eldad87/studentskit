/**
 * This code is responsible for Language and timezone selection
 */
(function( $ ){

    var methods = {
        //lang/prioritize
        _save: function(operation) {
            var settings = $(this).data('lang');

            var url = saveData = errorSelector = false;
            if(operation=='lang') {
                errorSelector = settings.layoutMessageSelector;

                url = settings.saveLangUrl + '/' + $(this).lang('_getMainLang');

            } else if(operation=='prioritize') {
                errorSelector = settings.addExistsError.selector;

                url = settings.savePrioritizeUrl + '/' +  $(this).lang('_getPrioritize').join();
            }

            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json'

            }).done(function ( data ) {
                var parseData = parseResponse(data);
                if(parseData['type']=='Error') {
                    showError(errorSelector, parseData['title'], parseData['des']);
                } else {
                    showSuccess(settings.layoutMessageSelector, parseData['title'], parseData['des']);
                }

            });

            return false;
        },

        _getPrioritize: function() {
            var settings = $(this).data('lang');

            var lang = [];
            $(this).find(settings.prioritizeLangList + ' li').each(function(index, value) {
                lang.push(
                    $(value).data(settings.dataPostfix)
                );
            });
            return lang;
        },

        _getMainLang: function() {
            var settings = $(this).data('lang');
            return $(this).find(settings.lang).val();
        },

        _bindRemoveButton: function() {
            var self = $(this);
            var settings = $(this).data('lang');

            $(this).find(settings.remove).unbind();

            //On remove
            $(this).find(settings.remove).click(function() {
                $(this).closest('li').remove();
                self.lang('_save', 'prioritize');
            });
        },

        _initBehavior: function() {
            var self = $(this);
            var settings = $(this).data('lang');

            $(this).lang('_bindRemoveButton');


            //On add
            $(this).find(settings.add).click(function() {
                var langValue = self.find(settings.langList).val();
                var langText = self.find(settings.langList).find(':selected').text();

                //Check if already in list
                var pLang = self.lang('_getPrioritize');
                if(jQuery.inArray(langValue, pLang)!==-1) {

                    showError(  settings.addExistsError.selector, //self.find(settings.addExistsError.selector),
                                settings.addExistsError.title,
                                settings.addExistsError.msg);
                    return false;
                }

                //Create li element
                var html = jQuery.nano(
                    settings.langTemplate,
                    {
                        val: langValue,
                        text: langText
                    }
                )

                //Append to list
                self.find(settings.prioritizeLangList).append(html);
                self.lang('_bindRemoveButton');


                //Save
                self.lang('_save', 'prioritize')
            });

            //On template change
            $(this).find(settings.lang).change(function() {
                var newLang = $(this).find(':selected').text(); //Get the selected text
                //Set the new text
                $(self).find(settings.langDisplay).html(
                    newLang
                );

                //Save
                self.lang('_save', 'lang');
            });
        },

        init : function( options ) {
            var settings = {
                langDisplay: '#showLang',                       //When changing main lang - it will appear there
                lang: '#lang',                                  //template selector
                prioritizeLangList: '#prioritize_lang_list',    //UL that contain languages
                dataPostfix: 'lang',                            //The attribute that hold that lang value <li data-lang="eng">
                remove: '.remove_lang',                         //Remove lang button

                add: '.add',            //Add button
                addExistsError: {
                    title: 'Error',
                    msg: 'Already exist in list'
                },
                langList: '.lang_list', //On click the add button, add the selected option here
                langTemplate: '<li class="space37 fullwidth pull-left space29" data-lang="{val}"><a href="#" class="color-text remove_lang"><i class="iconSmall-red-cross"></i></a>{text}</li>'
            };

            $.extend(true, settings, options);

            return this.each(function(){
                var $this = $(this),
                    data = $this.data('lang');



                if ( ! data ) {
                    $(this).data('lang', settings);
                }

                $(this).lang('_initBehavior');

            });
        }
    };

    $.fn.lang = function( method ) {

        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.localization' );
        }

    };

})( jQuery );