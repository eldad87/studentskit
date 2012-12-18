/**
 * This code is responsible for Language and timezone selection
 */
(function( $ ){

    var methods = {
        _save: function() {
            $(this).lang('_getPrioritize');
            $(this).lang('_getMainLang');

            //TODO:
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
            return $(this).find(settings.layout).val();
        },

        _bindRemoveButton: function() {
            var self = $(this);
            var settings = $(this).data('lang');

            $(this).find(settings.remove).unbind();

            //On remove
            $(this).find(settings.remove).click(function() {
                $(this).closest('li').remove();
                self.lang('_save');
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
            });

            //On template change
            $(this).find(settings.layout).change(function() {
                var newLang = $(this).find(':selected').text(); //Get the selected text
                //Set the new text
                $(self).find(settings.layoutDisplay).html(
                    newLang
                );

                //Save
                self.lang('_save');
            });
        },

        init : function( options ) {
            var settings = {
                layoutDisplay: '#selcountry',                   //When changing main lang - it will appear there
                layout: '#layout',                              //template selector
                prioritizeLangList: '#prioritize_lang_list',    //UL that contain languages
                dataPostfix: 'lang',                            //The attribute that hold that lang value <li data-lang="eng">
                remove: '.remove_lang',                         //Remove lang button

                add: '.add',            //Add button
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