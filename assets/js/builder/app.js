/* global jQuery, _, tinyMCE, wp, getUserSetting */

var ptPbApp = ptPbApp || {};

(function ($, ptPbApp) {
    'use strict';

    ptPbApp.isPb = function () {
        return ptPbOptions && ptPbOptions.isPb && ptPbOptions.isPb == 1;
    }

    //toggle between Pace Builder and Default Editor
    ptPbApp.toggleBuilder = function (e) {
        e && e.preventDefault();
        if (ptPbApp.cache.$builder.css('display') === 'none') {
            ptPbApp.cache.$toggle.text(ptPbOptions.i18n.editor_text);
            ptPbApp.cache.$mceEditor.hide();
            ptPbApp.cache.$builder.show();
            ptPbApp.cache.$isPb.val(1);
        } else if (window.confirm(ptPbOptions.i18n.default_editor)) {
            ptPbApp.cache.$toggle.text(ptPbOptions.i18n.builder_text);
            ptPbApp.cache.$builder.hide();
            ptPbApp.cache.$mceEditor.show();
            ptPbApp.cache.$isPb.val(0);
            
            setTimeout(function () {
                try{
                    if (typeof window.switchEditors !== 'undefined') {
                        window.switchEditors.go('content', ptPbApp.getEditorMode());
                    }

                    window.wpActiveEditor = 'content';

                    if (tinyMCE.get('content') !== undefined) {
                        tinyMCE.get('content').setContent(ptPbApp.cache.content);
                    } else {
                        ptPbApp.cache.$content.val( ptPbApp.cache.content );
                    }

                } catch(e){
                    console && console.error && console.error(e);
                }

            }, 100);
        }
    };

    ptPbApp.updatePBOptions = function () { 
        var oldVal = ptPbApp.cache.$pbOptions.val(),
            newVal = JSON.stringify(ptPbApp.PageOptions.toJSON({clean: true}));
        if( oldVal !== newVal ) {
            ptPbApp.cache.$pbOptions.val(newVal);
            if( ptPbApp.isPb() ){
                // set the content to new value so that PB data is auto saved
                ptPbApp.cache.$content.val(new Date().valueOf());
            }
        }
    };

    ptPbApp.updatePBContent = function () { 
        var oldVal = ptPbApp.cache.$pbData.val(),
            newVal = JSON.stringify(ptPbApp.Sections.toJSON({clean: true}));
        if( oldVal !== newVal ) {
            ptPbApp.cache.$pbData.val(newVal);
            if( ptPbApp.isPb() ){
                // set the content to new value so that PB data is auto saved
                ptPbApp.cache.$content.val(new Date().valueOf());
            }
        }
    };

    ptPbApp.autoSaveData = function () {
        return {
            pt_is_pb: ptPbApp.cache.$isPb.val(),
            ptpb_data: ptPbApp.cache.$pbData.val(),
            ptpb_options: ptPbApp.cache.$pbOptions.val(),
            'pt-pb-nonce': $('#pt-pb-nonce').val()
        };
    };

    ptPbApp.buildStage = function (layout, action) {
        if (action == 'replace') {
            ptPbApp.clearStage();
        }

        if ($.inArray(action, ['append', 'replace']) !== -1) {
            _.each(layout, function (section) {
                ptPbApp.Sections.add(section, {parse: true});
            });
        } else if (action == 'prepend') {
            var i = 0;
            _.each(layout, function (section) {
                ptPbApp.Sections.add(section, {parse: true, at: i});
                i++;
            });
        }
    };

    ptPbApp.clearStage = function () {
        ptPbApp.Sections.reset();
    };

    ptPbApp.isVisualEditor = function () {
        return $('#wp-ptpb_editor-wrap').hasClass('tmce-active');
    };

    ptPbApp.getContent = function () {
        if (ptPbApp.isVisualEditor()) {
            return tinyMCE.get('ptpb_editor').getContent();
        }

        return $('#ptpb_editor').val();
    };

    ptPbApp.setContent = function (content) {
        if (ptPbApp.isVisualEditor() && tinyMCE.get('ptpb_editor') !== undefined) {
            tinyMCE.get('ptpb_editor').setContent(content);
        } else {
            $('#ptpb_editor').val(content);
        }
    };

    ptPbApp.createEditor = function (el) {
        if (!el || el.length === 0 || !el.is(':input')) {
            return;
        }

        el.after(ptPbApp.cache.editorHtml);

        setTimeout(function () {
            if (typeof window.switchEditors !== 'undefined') {
                window.switchEditors.go('ptpb_editor', ptPbApp.getEditorMode());
            }

            window.wpActiveEditor = 'ptpb_editor';

            ptPbApp.setContent(el.val());

        }, 100);
    };

    ptPbApp.removeEditor = function () {
        if (typeof window.tinyMCE !== 'undefined') {
            try {
                window.tinyMCE.execCommand('mceRemoveEditor', false, 'ptpb_editor');
            } catch (e) {
            }
            if (typeof window.tinyMCE.get('ptpb_editor') !== 'undefined') {
                window.tinyMCE.remove('#ptpb_editor');
                $('#wp-ptpb_editor-wrap').remove();
            }
        }
    };

    ptPbApp.getEditorMode = function () {
        var mode = 'tinymce';

        if ('html' === getUserSetting('editor')) {
            mode = 'html';
        }

        return mode;
    };

    ptPbApp.scrollTo = function (top) {
        ptPbApp.cache.$html.stop().animate({
            scrollTop: top
        }, 600);
    };

    ptPbApp.paneName = function(pane) {
        return pane.replace(' ', '').toLowerCase();
    };

    ptPbApp.upload = {
        addFile: function (event) {

            var frame,
                $el = $(event.target),
                $parent = $el.parent();

            event.preventDefault();

            // If the media frame already exists, reopen it.
            if (frame) {
                frame.open();
                return;
            }

            // Create the media frame.
            frame = wp.media({
                // Set the title of the modal.
                title: $el.data('choose'),

                // Customize the submit button.
                button: {
                    // Set the text of the button.
                    text: $el.data('update'),
                    // Tell the button not to close the modal, since we're
                    // going to refresh the page when the image is selected.
                    close: false
                }
            });

            // When an image is selected, run a callback.
            frame.on('select', function () {
                // Grab the selected attachment.
                var attachment = frame.state().get('selection').first().toJSON();
                frame.close();
                $parent.find('.pt-pb-upload-field').val(attachment.url).trigger('change');
                $parent.find('.pt-pb-upload-field-id').val(attachment.id).trigger('change');
                if (attachment.type === 'image') {
                    $parent.find('.screenshot').empty().hide().append('<img src="' + attachment.url + '">').slideDown('fast');
                }
                $parent.find('.pt-pb-upload-button').hide();
                $parent.find('.pt-pb-remove-upload-button').show();
            });

            // Finally, open the modal.
            frame.open();
        },

        removeFile: function (selector) {
            selector.find('.pt-pb-upload-field').val('').trigger('change');
            selector.find('.screenshot').slideUp(200, function () {
                $(this).empty();
            });
            selector.find('.pt-pb-remove-upload-button').hide();
            selector.find('.pt-pb-upload-button').show();
        }
    };

    ptPbApp.modulesHeight = function () {
        if (!ptPbApp.cache.$modules.is(':visible'))
            return;
        var modules = ptPbApp.cache.$modules.find('ul.column-modules');
        modules.each(function () {
            ptPbApp.setModulesHeight($(this).find('.column-module > div.module-type'));
        });
    };

    ptPbApp.setModulesHeight = function (modules) {
        var pdng = parseInt(modules.first().parent().css('padding')),
            colCnt = pdng == 10 ? 4 : (pdng == 9 ? 3 : 2),
            cnt = Math.ceil(modules.length / colCnt);
        for (var i = 0; i < cnt; i++) {
            var maxHt = 0,
                start = (i * colCnt),
                slice = modules.slice(start, start + colCnt);
            slice.each(function () {
                var $t = $(this).removeAttr('style');
                maxHt = $t.outerHeight() > maxHt ? $t.outerHeight() : maxHt;
            });
            slice.css('height', maxHt + 'px');
        }
    };

    ptPbApp.fixedHeader = function(){
        if( ptPbApp.cache.$builder.offset().top + 65 < ptPbApp.cache.$window.scrollTop() ) {
            ptPbApp.cache.$builder.addClass('fixed-options');
        } else {
            ptPbApp.cache.$builder.removeClass('fixed-options');
        }
    };

    ptPbApp.topBar = function(){
        $(document).on('pt-tab:open', 'div.pt-tab-pane', function () {
            var $t = $(this),
                h  = $t.closest('.bbm-modal__section').find('.pt-pb-top-bar').outerHeight();
            $t.css('top', ++h + 'px');
        });
    };

    ptPbApp.GoogleMaps = {

        init : function (options) {

            if ( !options.el || !options.el.length || options.el.length === 0 || options.el.find('.gm-style').length > 0 ){
                return;
            }

            var centerLatLng = new google.maps.LatLng(40.698726437081305, -74.00508475781245);

            if (options.center) {
                var center = options.center.split(',');
                if (center.length === 2) {
                    centerLatLng = new google.maps.LatLng(center[0], center[1]);
                } else if (options.length > 0) {
                    centerLatLng = new google.maps.LatLng(parseFloat(options[0].lat), parseFloat(options[0].lng));
                }
            }

            var styles;

            if(options.styles && GoogleMapThemes[options.styles]){
                styles = GoogleMapThemes[options.styles];
            }

            options.el.mapsed({
                // Adds a predictive search box
                searchOptions: {
                    enabled: true,
                    geoSearch: "{POSITION}",
                    placeholder: "Search ..."
                },

                showOnLoad: options.points || [],

                styles: styles,

                mapOptions: {
                    zoom: options.zoom || 12,
                    center: centerLatLng
                },

                // allow user to select somewhere
                onSelect: function (m, placeToAdd) {
                    options.el.trigger('ptpb:map:point:selected', placeToAdd);
                    return true;
                },

                onSave: function (m, placeToSave) {
                    options.el.trigger('ptpb:map:point:selected', placeToSave);
                    return true;
                },

                onDelete: function (mapsed, placeToDelete) {
                    options.el.trigger('ptpb:map:point:deleted', placeToDelete);
                    return true;
                },

                // shows additional instructions to the user    
                getHelpWindow: function () {
                    var html =
                        "<div class='mapsed-help'>" +
                        "<h3>Find a Location</h3>" +
                        "<ol>" +
                        "<li>Simply use the <strong>search</strong> box to find your location.</li>" +
                        "<li>Click to set a marker on your desired location</li>" +
                        "<li>Once a marker is set, click on the marker to open the popup, click Edit to edit the details and once edited, click Select in the popup to choose the location</li>" +
                        "</ol>" +
                        "</div>";

                    return html;
                }

            });
        },

        addPoint: function(){

        }
    };


})(jQuery, ptPbApp);

jQuery(document).ready(function ($) {

    ptPbApp.app = new Backbone.Marionette.Application();

    // Create a layout class
    var dialog = Backbone.Marionette.LayoutView.extend({
        template: _.template($('#modals-template').html()),
        regions: {
            edit: {
                selector: '#ptpb_edit_dialog',
                regionClass: Backbone.Marionette.Modals
            },
            columns: {
                selector: '#ptpb_columns_dialog',
                regionClass: Backbone.Marionette.Modals
            },
            modules: {
                selector: '#ptpb_modules_dialog',
                regionClass: Backbone.Marionette.Modals
            },
            layouts: {
                selector: '#ptpb_layouts_dialog',
                regionClass: Backbone.Marionette.Modals
            },
            icons: {
                selector: '#ptpb_icons_dialog',
                regionClass: Backbone.Marionette.Modals
            },
        }
    });

    var stage = Backbone.Marionette.LayoutView.extend({
        template: ptPbApp.template('layout'),
        regions: {
            actions: "#ptpb_actions",
            main: "#pt-pb-main-container"
        }
    });

    // Render the layout
    var appDialog = new dialog(),
        appLayout = new stage();


    $('body').append(appDialog.render().el);
    $('#pt-pb-main-wrap').append(appLayout.render().el);


    ptPbApp.app.vent.on("ptpb:edit:form", function (options) {
        appDialog.edit.show(new ptPbApp.Views.Dialog(options));
    });

    ptPbApp.app.vent.on("ptpb:insert:row", function (options) {
        appDialog.columns.show(new ptPbApp.Views.Dialog(options));
    });

    ptPbApp.app.vent.on("ptpb:update:row", function (options) {
        appDialog.columns.show(new ptPbApp.Views.Dialog(options));
    });

    ptPbApp.app.vent.on("ptpb:insert:module", function (options) {
        var modules = new Backbone.Collection( _.map(ptPbOptions.formFields.modules, function(val, key){val.slug = key; return val; }) );
        modules.add(_.map(ptPbOptions.widgets, function(val, key){val.slug ='widget'; return val; }));

        var moduleFilter = new ptPbApp.Models.Filter({collection: modules, where:['label', 'slug', 'description']}),
            $allModTab;

        options.filter = {
            template: 'module-items',
            collection: moduleFilter.filtered,
            model: moduleFilter,
            appendTo: '#pt-pb-all-modules',
            formAppendTo: '.bbm-modal__topbar'
        }
        // hook up change event and go to "All" tab in Modules Dialog
        moduleFilter.on('change', function(){
            if(!$allModTab){
                $allModTab = $('#pt-pb-all-modules');
            }
            if(!$allModTab.is(':visible')){
                $('#pt-pb-insert-modules .pt-topbar-tabs li a').filter(function(t, el){return $(el).attr('href')==='#pt-pb-all-modules'}).trigger('click');
            }
        });

        appDialog.modules.show(new ptPbApp.Views.Dialog(options));
    });

    ptPbApp.app.vent.on("ptpb:insert:item", function (options) {
        appDialog.modules.show(new ptPbApp.Views.Dialog(options));
    });

    ptPbApp.app.vent.on("ptpb:layouts:show", function (options) {
        appDialog.layouts.show(new ptPbApp.Views.Layouts(options));
    });

    ptPbApp.app.vent.on("ptpb:options:show", function (options) {
        var view = new ptPbApp.Views.Base({model: ptPbApp.PageOptions});
        view.editTemplate = 'page-options-edit';
        view.edit();
    });

    ptPbApp.iconFilter = {};
    ptPbApp.iconFilter.icons = {},
    ptPbApp.iconFilter.filters = {};

    _.each(ptPbOptions.icons, function(list, family){
        var slug = ptPbApp.slug(family);
        ptPbApp.iconFilter.icons[slug] = new Backbone.Collection( _.map(list, function(val, key){ return { name: key, cls: val } }));
        ptPbApp.iconFilter.filters[slug] = new ptPbApp.Models.Filter({collection: ptPbApp.iconFilter.icons[slug], where:['name']});
    });

    ptPbApp.app.vent.on("ptpb:icons:show", function (options) {
        appDialog.icons.show(new ptPbApp.Views.Icons(options));
    });

    ptPbApp.app.vent.on("ptpb:map:init", function (options) {
        ptPbApp.GoogleMaps.init(options);
    });

    ptPbApp.getSectionNum = function () {
        return ['ptpb_s', ++ptPbApp.sectionNum].join('');
    };

    ptPbApp.app.addInitializer(function (options) {

        ptPbApp.sectionNum = 0;

        ptPbApp.cache.editorHtml = ptPbApp.cache.$hiddenEditor.html();
        ptPbApp.cache.$hiddenEditor.remove();

        if (ptPbApp.isPb()) {
            ptPbApp.toggleBuilder();
        }

        appLayout.actions.show(new ptPbApp.Views.Actions());

        ptPbApp.Sections = new ptPbApp.Collections.Section();
        _.each(ptPbOptions.data, function (section) {
            if (section.id) delete section.id;
            ptPbApp.Sections.add(section, {parse: true});
        });

        ptPbApp.PageOptions = new ptPbApp.Models.PageOptions(ptPbOptions.pageOptions || {});
        ptPbApp.updatePBOptions();

        // ptPbApp.Sections.reset(ptPbOptions.data, { parse: true });
        appLayout.main.show(new ptPbApp.Views.Sections({collection: ptPbApp.Sections}));

        var grpdModules = _.pick(ptPbOptions.formFields.modules, function(val, key){return val.tabPanes && _.isArray(val.tabPanes); });
        ptPbApp.modulePanes = {};
        _.each(grpdModules, function(module, slug){ 
            _.each(module.tabPanes, function(pane){
                ptPbApp.modulePanes[pane] = ptPbApp.modulePanes[pane] || {};
                ptPbApp.modulePanes[pane][slug] = module;
            });
        });

        ptPbApp.layoutPanes = {};
        _.each(ptPbOptions.layouts, function(layout, name){
                var pane = layout.tab_pane || 'Theme Prebuilt';
                ptPbApp.layoutPanes[pane] = ptPbApp.layoutPanes[pane] || {};
                ptPbApp.layoutPanes[pane][name] = _.isArray(layout) ? layout : [layout];
        });

        $('#ptpb_loader').fadeOut();
    });

    ptPbApp.cache = {
        $window : $(window),
        $container: $('#pt-pb-main-container'),
        $pageTemplate: $('#page_template'),
        $hiddenEditor: $('#pt-pb-editor-hidden'),
        editorHtml: '',
        sectionNum: 0,
        $html: $('html, body'),
        $builder: $('#pt-pb-stage'),
        $mceEditor: $('#postdivrich'),
        $toggle: $('#pt_enable_pb'),
        $isPb: $('#pt_is_pb'),
        $modules: $('#ptpb_modules_dialog'),
        $columns: null,
        $pbData: $('#ptpb_data'),
        $pbOptions: $('#ptpb_options'),
        $content: $('#content'),
        content: $('#content').val()
    };

    ptPbApp.cache.$toggle.on('click', ptPbApp.toggleBuilder);

    ptPbApp.cache.$container.on('click', '.pt-pb-module-toggle', function (e) {
        e.preventDefault();
        $(this).closest('.module-controls').siblings('.content-preview, .slide-content-preview').slideToggle(300, function () {
            $(this).siblings('.module-controls').toggleClass('close');
        });
    });

    $('.pt-pb-insert-section').on('click', function (e) {
        e.preventDefault();
        ptPbApp.Sections.add({});
    });

    ptPbApp.cache.$window.resize(ptPbApp.modulesHeight);
    ptPbApp.cache.$window.scroll(ptPbApp.fixedHeader);
    ptPbApp.topBar();

    ptPbApp.app.start();

});
