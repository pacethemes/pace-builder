/*jshint freeze:false*/
/* global _ */

var ptPbApp = ptPbApp || {};

(function ($, _) {

    String.prototype.toProperCase = function () {
        return this.replace(/\w\S*/g, function (txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
    };

    /**
     * ptPbApp.template( id )
     *
     * Fetch a JavaScript template for an id, and return a templating function for it.
     *
     * @param  {string} id   A string that corresponds to a DOM element with an id prefixed with "tmpl-".
     *                       For example, "attachment" maps to "tmpl-attachment".
     * @return {function}    A function that lazily-compiles the template requested.
     */
    ptPbApp.template = _.memoize(function (id) {
        var compiled,
        /*
         * Underscore's default ERB-style templates are incompatible with PHP
         * when asp_tags is enabled, so WordPress uses Mustache-inspired templating syntax.
         *
         * code reference - @see trac ticket #22344.
         */
            options = {
                evaluate: /<#([\s\S]+?)#>/g,
                interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
                escape: /\{\{([^\}]+?)\}\}(?!\})/g
            };

        return function (data) {
            try{
                if( id != 'module-header' && id.match(/module-/) ) {
                    // get default options for modules
                    data = _.extend( {}, ptPbOptions.formFields.modules[id.replace('module-','').replace('-edit','')] || {}, data );
                }
                compiled = compiled || _.template($('#pt-pb-tmpl-' + id).html(), null, options);
                return compiled({data: data});
            } catch(e){ 
                if( console && console.error && !$('#pt-pb-tmpl-' + id).length ) {
                    console.error( 'Template "#pt-pb-tmpl-' + id + '" does not exist.' );
                    return;
                }
                console && console.error && console.error(e); 
            }
        };
    });

    ptPbApp.partial = function (which, data) {
        return ptPbApp.template(which)(data);
    };

    ptPbApp.generateOption = function (selected, value, name, attr, optgroup) {
        if (!name) {
            name = value;
        }
        if(!attr) {
            attr = '';
        }
        return '<option value="' + value + '" ' + (value === selected ? 'selected' : '') + ' ' + attr + '>' + name + '</option>';
    };

    ptPbApp.getInputPrefix = function (id) {
        return (id.split('__').join('][') + ']').replace('pt_pb_section]', 'pt_pb_section');
    };

    ptPbApp.serializeElms = function (elm) {
        var arr = elm.serializeObject(),
            result = {};
        $.each(arr, function (i, v) {
            var n = i.split('][').slice(-1)[0].replace(']', '') || 'nn';
            result[n] = v;
        });
        return result;
    };

    ptPbApp.stripslashes = function stripslashes(str) {
        // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   improved by: Ates Goral (http://magnetiq.com)
        // +      fixed by: Mick@el
        // +   improved by: marrtins
        // +   bugfixed by: Onno Marsman
        // +   improved by: rezna
        // +   input by: Rick Waldron
        // +   reimplemented by: Brett Zamir (http://brett-zamir.me)
        // +   input by: Brant Messenger (http://www.brantmessenger.com/)
        // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
        // *     example 1: stripslashes('Kevin\'s code');
        // *     returns 1: "Kevin's code"
        // *     example 2: stripslashes('Kevin\\\'s code');
        // *     returns 2: "Kevin\'s code"
        return (str + '').replace(/\\(.?)/g, function (s, n1) {
            switch (n1) {
                case '\\':
                    return '\\';
                case '0':
                    return '\u0000';
                case '':
                    return '';
                default:
                    return n1;
            }
        });
    };

    ptPbApp.htmlEncode = function (value) {
        return String(ptPbApp.stripslashes(value))
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    };

    ptPbApp.isUrl = function (s) {
       var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
       return regexp.test(s);
    }

    ptPbApp.slug = function (name){
        return name.replace(/[&\/\\#,+()$~%.'":*?<>{}\s]/g, '-').toLowerCase();
    }

}(jQuery, _));
