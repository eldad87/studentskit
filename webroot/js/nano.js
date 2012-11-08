/* Nano Templates (Tomasz Mazur, Jacek Becela) */

(function($){
    $.nano = function(template, data) {
        template = template.replace('%7B', '{').replace('%7D', '}'); //Fix escaping of {}
        return template.replace(/\{([\w\.]*)\}/g, function (str, key) {
            var keys = key.split("."), value = data[keys.shift()];
            $.each(keys, function () { value = value[this]; });
            return (value === null || value === undefined) ? "" : value;
        });
    };
})(jQuery);