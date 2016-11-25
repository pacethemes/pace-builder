/*jshint unused:false*/
/* global Backbone, jQuery, _, console */

var ptPbApp = ptPbApp || {};
ptPbApp.Views = ptPbApp.Views || {};
ptPbApp.Behaviors = ptPbApp.Behaviors || {};

(function (window, Backbone, $, _, ptPbApp) {
    'use strict';

    ptPbApp.Behaviors.Sortable = Marionette.Behavior.extend({
        onRender: function () {
            var collection = this.view.collection,
                b = this;
            this.$el.sortable({
                distance: 5,
                forcePlaceholderSize: true,
                forceHelperSize: true,
                placeholder: 'sortable-placeholder-pt-pb',
                axis: this.options.axis || false,
                grid: this.options.grid || false,
                containment: this.options.containment || false,
                cursor: "move",
                handle: this.options.handle || false,
                revert: this.options.revert || false,
                connectWith: this.options.connectWith || false,
                update: function (event, ui) {
                    if (this == ui.item.parent()[0] && ui.sender == null) {
                        //item is sorted within the same container
                        var model = collection.get(ui.item.attr('id'));
                        collection.remove(model, {silent: true});
                        collection.add(model, {at: ui.item.index(), silent: true});
                        ptPbApp.updatePBContent();
                    } else if (this !== ui.item.parent()[0]) {
                        // item is moved to a diffent container
                        // act only on the sender container to read the collection associated
                        ptPbApp.senderCollection = collection;
                    } else if (this == ui.item.parent()[0] && ui.sender != null) {
                        // item is moved to a different container
                        // remove the item from old collection and add it to new collection
                        var model = ptPbApp.senderCollection.get(ui.item.attr('id'));
                        ptPbApp.senderCollection.remove(model);
                        collection.parent.add(model.toJSON(), {at: ui.item.index()});
                    }
                },
                start: this.options.start || function () {
                },
                over: this.options.over || function () {
                }
            });
        }
    });

    ptPbApp.Views.Filter = Backbone.View.extend({
        render:function() {

            var html, $oldel = this.$el, $newel;

            html = this.html();
            $newel=$(html);

            this.setElement($newel);
            $oldel.replaceWith($newel);

            return this;
        },
        initialize: function(opts) {
            // I like to pass the templates in the options
            this.template = opts.template;
            // listen to the filtered collection and rerender
            this.listenTo(this.collection, 'reset', this.render);
        },
        html: function() {
            return this.template({
                models: this.collection.toJSON()
            });
        }
    });

    ptPbApp.Views.FilterForm = Backbone.View.extend({
        events: {
            // throttled to limit the updates
            'keyup input[name="q"]': _.throttle(function(e) {
                 this.model.set('what', e.currentTarget.value);
            }, 200),
        },
        className: 'pt-pb-filter-form',
        render: function(){
            this.$el.html('<form><input type="text" placeholder="Search.." name="q" /></form>');
            return this;
        }
    });

    // unbind outside click events for Modal
    Backbone.Modal.prototype.keyControl = false;

    ptPbApp.Views.Modal = Backbone.Modal.extend({
        onRender: function () {
            this.$el.append('<a class="close-bbm-modal">×</a>');
            this.tabs();
        },
        onShow: function () {
            this.$('.pt-sidebar-tabs li a, .pt-topbar-tabs li a').first().trigger('click');
        },
        tabs: function () {
            this.$('.pt-sidebar-tabs, .pt-topbar-tabs').off('click', 'li a')
                .on('click', 'li a', function (e) {
                    e.preventDefault();
                    var $t = $(e.target),
                        li = $t.parent(),
                        lis = li.siblings('li'),
                        href = $t.attr('href'),
                        pane = $(href),
                        panes = pane.parent().find('.pt-tab-pane');

                    lis.removeClass('tab-active');
                    li.addClass('tab-active')

                    panes.removeClass('open');
                    pane.addClass('open').trigger('pt-tab:open');
                });
        },
        showLoader: function($elm){
            $elm.append('<div class="pt-pb-loader"><div class="pt-pb-spinner"></div></div>');
        },
        removeLoader: function($elm){
            $elm.find('.pt-pb-loader').remove();
        }
    });

    ptPbApp.Views.Dialog = ptPbApp.Views.Modal.extend({
        // wiring the event this way because of stacked modals issue
        events: {
            'click .button-primary': 'triggerSubmit',
            'click .close-model': 'triggerCancel',
            'click .close-bbm-modal': 'triggerCancel'
        },

        initialize: function (options) {
            this.template = ptPbApp.template(options.template);
            if(options.filter){
                this.filterTemplate = options.filter.template;
                this.filterCollection = options.filter.collection;
                this.filterAppendTo = options.filter.appendTo;
                this.filterModel = options.filter.model;
                this.filterFormAppendTo = options.filter.formAppendTo;
            }
        },

        onDestroy: function () {
            ptPbApp.removeEditor();
        },

        onRender: function () {
            ptPbApp.Views.Modal.prototype.onRender.call(this);
            this.unSavedData = this.$(':input:not(button, .button)').serializeObject();
            // set tinyMCE editor, there can be only 1 tinyMCE editor in a dialog
            this.$content = this.$('input[name=content]');
            ptPbApp.createEditor(this.$content);

            // bind all form elements/input events
            this.bindEvents();

            if (this.model.get('type') === 'widget')
                this.widgetForm();

            if(this.filterTemplate){
                var view = this,
                    filterform = new ptPbApp.Views.FilterForm({model: view.filterModel}),
                    filterView = new ptPbApp.Views.Filter({
                        template: ptPbApp.template(view.filterTemplate),
                        collection: view.filterCollection
                    });
                this.$(view.filterFormAppendTo).append(filterform.render().el);    
                this.$(view.filterAppendTo).append(filterView.render().el);
            }
        },

        submit: function (e) {
            var data = this.$(':input:not(button, .button)').serializeObject();

            this.$(':checkbox:not(:checked)').map(function(){
              data[this.getAttribute('name')] = false;
            });

            if (this.$content.length > 0) {
                data.content = ptPbApp.getContent();
                delete data.ptpb_editor;
            }

            if( this.$chosens && this.$chosens.length ) {
                this.$chosens.each(function(){
                    var $c = $(this),
                        name = $c.attr('name').replace('[','').replace(']','');
                    if( !data[name] ) {
                        data[name] = '';
                    }
                });
            }
            this.model.set(data);
        },

        widgetForm: function (update) {
            var view = this,
                $elm = view.$('#pt-form-module-settings'),
                data = {
                    'action': 'ptpb_widget_form',
                    'widget': this.model.get('widget'),
                    'instance': JSON.stringify(this.model.toJSON().instance),
                    'update': update ? true : false
                };
            this.showLoader($elm);

            $.post(
                ptPbOptions.ajaxurl,
                data,
                function (result) {
                    $elm.prepend(result).addClass('widget-edit');
                    view.removeLoader($elm);
                },
                'html'
            );
        },

        autocompleteGroupMenu: function(ul, items){
            var self = this;
            var category = null;
            
            var sortedItems = items.sort(function(a, b) {
               return a.category.localeCompare(b.category);
            });
            
            $.each(sortedItems, function (index, item) {
                if (item.category != category) {
                    category = item.category;
                    ul.append("<li class='ui-autocomplete-group'>" + category + "</li>");
                }
                self._renderItemData(ul, item);
            });        
        },

        bindEvents: function () {
            var thisView = this,
                $document = $(window.document);

            $document.trigger('ptpb:dialog:bindEvents:before', thisView);

            thisView.dependencies();
            thisView.mediaUploads();
            thisView.icons();

            thisView.chosenDropDowns();
            thisView.fontDropDowns();
            thisView.toggles();
            thisView.colorPickers();
            thisView.rangeSliders();
            thisView.animationPreview();

            this.$('.column-layouts li').on('click', function () {
                if ($('#pt-pb-insert-columns').hasClass('update-columns') && window.confirm(ptPbOptions.i18n.resize_columns)) {
                    thisView.model.trigger('ptpb:update:row', {layout: $(this).data('layout').replace(/ /g, '').split(',')});
                    thisView.model.unset('update');
                } else {
                    thisView.model.trigger('ptpb:add:row', {layout: $(this).data('layout').replace(/ /g, '').split(',')});
                }
                thisView.triggerCancel();
            });

            this.$('#pt-pb-insert-modules').on('click', '.module-type', function () {
                thisView.model.trigger('ptpb:add:module', $(this).data());
                thisView.triggerCancel();
            });

            thisView.$('div.pt-tab-pane').on('pt-tab:open', function () {
                // if Modules Modal then set height
                ptPbApp.modulesHeight();
                thisView.googleMap();
            });

            $document.trigger('ptpb:dialog:bindEvents:after', thisView);

        },

        dependencies: function() {
            var thisView = this;

            // Show or Hide a form input based on dependency conditions
            function showHideInput($t, data){
                try{
                    var cond = "<%= "+ $t.data('condition') +" %>";
                    if( _.template(cond)(data) == 'true' ) {
                        $t.show();
                    } else {
                        $t.hide();
                    }
                } catch(e) {
                    console && console.error && console.error(e);
                }
            }

            var deps = {};
            thisView.$('.pt-pb-option[data-dependency]').each(function(){
                var $t = $(this).hide(),
                    dependency = $t.data('dependency');
                
                $.each(dependency.split(','), function(i,d){
                    d = $.trim(d);
                    deps[d] = deps[d] || [];
                    deps[d].push($t);
                });
            });

            $.each(deps, function(d,inputs){
                var $dep = thisView.$('.pt-pb-option :input[name="' + d + '"]');
                $dep.on('change keyup keypress paste', function(e){
                    $.extend(thisView.unSavedData, $dep.serializeObject());
                    $.each(inputs, function(i,input){
                        // showHideInput(input, thisView.unSavedData);
                        try{
                            var cond = "<%= "+ input.data('condition') +" %>";
                            if( _.template(cond)(thisView.unSavedData) == 'true' ) {
                                input.show();
                            } else {
                                input.hide();
                            }
                        } catch(e) {
                            console && console.error && console.error(e);
                        }
                    });
                }).trigger('keyup');
            });

        },

        mediaUploads: function() {
            //Bind media upload events
            this.$('.pt-pb-upload-field').each(function () {
                var el = $(this);
                if (el.val() !== '') {
                    el.siblings('.pt-pb-upload-button').hide();
                    el.siblings('.pt-pb-remove-upload-button').show();
                    el.siblings('.screenshot').empty().append('<img src="' + el.val() + '">').show();
                } else {
                    el.siblings('.pt-pb-upload-button').show();
                    el.siblings('.pt-pb-remove-upload-button').hide();
                }
            });

            this.$('.pt-pb-remove-upload-button').on('click', function (event) {
                ptPbApp.upload.removeFile($(event.target).parent());
            });

            this.$('.pt-pb-upload-button').on('click', function (event) {
                ptPbApp.upload.addFile(event);
            });
        },

        icons: function() {
            //Bind Icon select events
            this.$('.pt-pb-icon-select').click(function () {
                var $select = $(this);
                ptPbApp.app.vent.trigger('ptpb:icons:show', {select: $select});
            });

            this.$('.pt-pb-icon-delete').click(function () {
                var $option = $(this).closest('.pt-pb-option-container');
                $option.children('.pt-pb-icon').val('').trigger('change');
                $option.children('.icon-preview').html('');
            });
        },

        chosenDropDowns: function() {
            this.$chosens = this.$('select.chosen-select:not(.font-select)').chosen({
                search_contains: true,
                width: '420px'
            })
            .on('change', function(e, params){
                if(params && params.deselected){
                    $(this).find('option[value="'+params.deselected+'"]').prop("selected", false);
                }
            }).filter('[multiple]');
        },

        fontDropDowns: function() {
            var thisView = this;
            this.$('select.font-select').each(function () {
                var $f = $(this),
                    name = $f.attr('name'),
                    varName = name.replace('_f', '_v'),
                    $v = $f.closest('.pt-pb-option').next().find('select[name="' + varName + '"]');
                $v.val(thisView.model.get(varName));
                $f.html(ptPbOptions.fonts)
                    .val(thisView.model.get(name) || '')
                    .on('change', function () {
                        var $t = $(this),
                            selected = $t.find('option:selected'),
                            vars = selected.length > 0 ? selected.attr('data-variants').split(',') : [];

                        if ($v.length > 0 && vars.length > 0) {
                            $v.empty();
                            _.each(vars, function (v) {
                                $v.append('<option value="' + v + '">' + v.toProperCase() + '</option>');
                            });
                            if ($v.children('option[value=' + thisView.model.get(varName) + ']').length > 0)
                                $v.val(thisView.model.get(varName))
                        }

                    })
                    .chosen({
                        search_contains: true,
                        width: '340px'
                    })
                    .trigger('change');
                if($f.is('[multiple]')){
                    thisView.$chosens.add($f);
                }
            });
        },

        toggles: function() {
            this.$('a.pt-pb-toggle-btn').on('click', function (e) {
                e.preventDefault();
                var $t = $(this);
                $t.toggleClass('active');
                $t.children('input').val($t.hasClass('active') ? 1 : 0);
            });
        },

        colorPickers: function(){
            //Bind color picker events
            this.$('.pt-pb-color').wpColorPicker(
                    {
                        //trigger keyup events so that dependencies work when the value is changed
                        change: function(event, ui) {
                            $(event.target).trigger('keyup');
                        },
                        clear: function(event){
                            $(event.target).prev('.pt-pb-color').trigger('keyup');
                        }
                    }
                );
        },

        dateTimePickers: function(){
            this.$('.date-picker').datepicker();
            this.$('.time-picker').timepicker();
            this.$('.datetime-picker').datetimepicker({ dateFormat: "yy-mm-dd", timeFormat: "HH:mm:ss" });
        },

        animationPreview: function(){
            this.$('select.js-animations').on('change', function (e) {
                e.preventDefault();
                var $select = $(this),
                    $preview = $select.siblings('.animation-preview');

                $preview.removeClass().addClass($select.val() + ' animated animation-preview').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                    $(this).removeClass().addClass('animation-preview');
                });
            });
        },

        rangeSliders: function() {
            this.$('div.pt-pb-option-container input.input-slider').each(function () {
                $(this).ionRangeSlider({
                    type: "single",
                    keyboard: false,
                    prettify_enabled: false
                });
            });

            this.$('div.pt-pb-option-container input.input-range-slider').each(function () {
                $(this).ionRangeSlider({
                    type: "double",
                    keyboard: false,
                    prettify_enabled: false
                });
            });
        },

        googleMap: function(){
            var thisView = this,
                $map_el = thisView.$('.google-map-select:visible');
            
            if( !$map_el.length || $map_el.hasClass('map-loaded') ) {
                return;
            }

            $map_el.addClass('map-loaded');

            // initialize points
            thisView.model.set('points', thisView.model.get('points') || [] );

            var $points = thisView.$('div#gmap-points'),
                pointsTmp = ptPbApp.template('module-googlemap-points');

            
            ptPbApp.app.vent.trigger('ptpb:map:init', {
                el: $map_el,
                points: thisView.model.get('points'),
                zoom: parseInt(thisView.model.get('zoom')),
                center: thisView.model.get('center'),
                styles: thisView.model.get('theme')
            });

            // remove default mapsed events
            $map_el.off('ptpb:map:point:selected')
                   .off('ptpb:map:point:deleted')
                   .on('ptpb:map:initialPlaces:added', function(){
                        $points.html(pointsTmp({points: thisView.model.get('points')}));
                    });

            // add our own marker/point add/delete events
            $map_el.on('ptpb:map:point:selected', function(e, point){
                var points = thisView.model.get('points');
                points = deletePoint(points, point.lat, point.lng);
                points.push(point);
                thisView.model.set('points', points);
                $points.html(pointsTmp({points: points}));
            });

            $map_el.on('ptpb:map:point:deleted', function(e, point){
                var points = thisView.model.get('points');
                thisView.model.set('points', deletePoint(points, point.lat, point.lng));
                $points.html(pointsTmp({points: thisView.model.get('points')}));
            });

            thisView.$('[name=theme]')
                .off('change')
                .on('change', function(){
                    gmap.setOptions({
                        styles: GoogleMapThemes[$(this).val()]
                    });
                });

            function deletePoint(points, lat, lng){
                return  _.filter(points, function(point){ return point.lng != lng && point.lat != lat });;
            }
        }
    });

    ptPbApp.Views.Layouts = ptPbApp.Views.Modal.extend({
        cancelEl: '.close-model, .close-bbm-modal',
        template: ptPbApp.template('layout-manager'),
        layoutItems: ptPbApp.template('layout-items'),
        layout: null,
        events: {
            'show:layouts': 'show',
            'click #pt-pb-save-layout': 'saveLayout',
            'btn_click .layout-insert': 'insertLayout',
            'click .layout-delete': 'deleteLayout',
            'pt-tab:open #pt-layout-prebuilt': 'onOpenPrebuiltTab',
            'pt-tab:open #pt-layout-load': 'onOpenLoadTab',
            'pt-tab:open #pt-layout-save': 'onOpenSaveTab',
            'pt-tab:open #pt-layout-import': 'onOpenImportTab'
        },

        onRender: function (cls) {
            ptPbApp.Views.Modal.prototype.onRender.call(this);

            this.importExport();

            $('html').click(function (e) {
                if (!$(e.target).is('.layout-insert'))
                    $('.pt-pb-dropdown').not('.hidden').addClass('hidden');
            });

            return this;
        },

        initDropDownButtons: function () {
            this.$('.button-dropdown').each(function () {
                var $btn = $(this),
                    $dropdown = $btn.siblings('.pt-pb-dropdown');
                $btn.off('click').on('click', function (e) {
                    e.preventDefault();
                    if ($dropdown.hasClass('hidden')) {
                        $dropdown.removeClass('hidden');
                    } else {
                        $dropdown.addClass('hidden');
                    }
                });
                $dropdown.find('a').off('click').on('click', function (e) {
                    e.preventDefault();
                    $btn.data($(this).data());
                    $dropdown.addClass('hidden');
                    $btn.trigger('btn_click');
                });
            });
        },

        /**
         * Display and setup the import/export form
         */
        importExport: function () {

            var view = this,
                uploadUi = view.$('.import-upload-ui').hide();

            // Create the uploader
            var uploader = new plupload.Uploader({
                runtimes: 'html5,silverlight,flash,html4',

                browse_button: uploadUi.find('.file-browse-button').get(0),
                container: uploadUi.get(0),
                drop_element: uploadUi.find('.drag-upload-area').get(0),

                file_data_name: 'ptpb_import_data',
                multiple_queues: false,
                max_file_size: ptPbOptions.plupload.max_file_size,
                url: ptPbOptions.plupload.url,
                flash_swf_url: ptPbOptions.plupload.flash_swf_url,
                silverlight_xap_url: ptPbOptions.plupload.silverlight_xap_url,
                filters: [
                    {title: ptPbOptions.plupload.filter_title, extensions: 'json'}
                ],

                multipart_params: {
                    action: 'ptpb_import_layout'
                },

                init: {
                    PostInit: function (uploader) {
                        if (uploader.features.dragdrop) {
                            uploadUi.addClass('has-drag-drop');
                        }
                        uploadUi.show().find('.progress-precent').css('width', '0%');
                    },
                    FilesAdded: function (uploader) {
                        uploadUi.find('.file-browse-button').blur();
                        uploadUi.find('.drag-upload-area').removeClass('file-dragover');
                        uploadUi.find('.progress-bar').fadeIn('fast');
                        uploader.start();
                    },
                    UploadProgress: function (uploader, file) {
                        uploadUi.find('.progress-precent').css('width', file.percent + '%');
                    },
                    FileUploaded: function (uploader, file, response) {
                        var layout = JSON.parse(response.response);
                        view.importedLayout = layout.layout ? layout.layout : view.importedLayout;
                        view.importedPageOptions = layout.pageOptions ? layout.pageOptions : false;
                        uploadUi.find('.progress-bar').hide();
                        view.$('#import-complete').html(view.layoutItems({layouts: null, type: 'import'})).show();
                        view.initDropDownButtons();
                    },
                    Error: function () {
                        alert(ptPbOptions.plupload.error_message);
                    }
                }
            });
            uploader.init();

            uploadUi.find('.drag-upload-area')
                .on('dragover', function () {
                    $(this).addClass('file-dragover');
                })
                .on('dragleave', function () {
                    $(this).removeClass('file-dragover');
                });

            //Handle exporting the file
            view.$('.pt-export').submit(function (e) {
                var $t = $(this);
                $t.find('#ptpb_export_data').val(JSON.stringify({ layout: ptPbApp.Sections.toJSON({clean: true}), pageOptions: ptPbApp.PageOptions.toJSON({clean: true}) }));
            });

        },

        insertLayout: function (e) {
            e.preventDefault();
            var btn = $(e.target),
                item = btn.closest('.pt-pb-layout-item'),
                type = item.data('layoutType'),
                action = btn.data('value'),
                layout, pageOptions, data;

            if (type === 'db') {
                data = this.dbLayouts[item.data('layout')];
            } else if (type === 'prebuilt') {
                layout = this.preLayouts[item.data('layout')];
                data = layout && layout.layout ? layout.layout : [];
                pageOptions = layout && layout.pageOptions ? layout.pageOptions : false;
            } else if (type === 'theme-prebuilt') {
                layout = ptPbOptions.layouts[item.data('layout')];
                data = layout && layout.layout ? layout.layout : [];
                pageOptions = layout && layout.pageOptions ? layout.pageOptions : false;
            } else {
                data = this.importedLayout;
                pageOptions = this.importedPageOptions;
            }

            //just in case the layout is still a JSON string
            data = typeof data === 'string' ? JSON.parse(data) : data;

            if (action === 'replace' && !window.confirm(ptPbOptions.i18n.replace_layout)) {
                return false;
            }

            ptPbApp.buildStage(data, action);
            if( pageOptions ) {
                ptPbApp.PageOptions = new ptPbApp.Models.PageOptions(pageOptions);
                ptPbApp.updatePBOptions();
            }
            this.triggerCancel();
        },

        saveLayout: function (e) {
            e && e.preventDefault();
            var layoutName = this.$('#pt-layout-name').val(),
                layout = JSON.stringify(ptPbApp.Sections.toJSON({clean: true}));

            if ($.trim(layoutName) === '') {
                this.$('.pt-save-message')
                    .html(ptPbOptions.i18n.empty_layout_name)
                    .removeClass('updated error')
                    .addClass('error');
                return;
            }

            if ($.trim(layout) === '') {
                this.$('.pt-save-message')
                    .html(ptPbOptions.i18n.empty_layout)
                    .removeClass('updated error')
                    .addClass('error');
                return;
            }

            var view = this,
                data = {
                    'action': 'ptpb_save_layout',
                    'layout_name': layoutName,
                    'layout': layout
                };

            $.post(
                ptPbOptions.ajaxurl,
                data,
                function (result) {
                    var response = JSON.parse(result);
                    view.$('.pt-save-message').html(response.message).removeClass('updated error').addClass(response.success ? 'updated' : 'error');
                },
                'html'
            );
        },

        deleteLayout: function (e) {
            e.preventDefault();
            var $c = this.$('#pt-layout-load'),
                item = $(e.target).closest('.pt-pb-layout-item');
            if (window.confirm(ptPbOptions.i18n.delete_layout)) {
                $.post(
                    ptPbOptions.ajaxurl, {
                        'action': 'ptpb_delete_layout',
                        'layout_name': item.data('layout')
                    },
                    function (result) {
                        if (result) {
                            item.fadeOut(300, function () {
                                if (item.siblings().length == 0)
                                    $c.append(ptPbOptions.i18n.empty_db_layouts).addClass('empty-layouts');
                                item.remove();
                            })
                        }
                    },
                    'html'
                );
            }
        },

        onOpenPrebuiltTab: function (e) {
            var $c = this.$('#pt-layout-prebuilt').empty().removeClass('error'),
                view = this;
            view.showLoader($c);
            $.get(
                ptPbOptions.ajaxurl, {'action': 'ptpb_get_prebuilt_layouts'},
                function (result) {
                    var response = JSON.parse(result);
                    if (!response.error) {
                        view.preLayouts = response;
                        $c.append(view.layoutItems({layouts: response, type: 'prebuilt'}));
                    } else {
                        $c.addClass('error').append('<strong>' + ptPbOptions.i18n.prebuilt_get_error + '</strong><br/><br/>');
                        $c.append('Response Code: ' + response.error.code)
                        $c.append('<br/>Message: ' + response.error.message).addClass('empty-layouts');
                    }
                    view.initDropDownButtons();
                    view.removeLoader($c);
                },
                'html'
            );
        },

        onOpenLoadTab: function (e) {
            var $c = this.$('#pt-layout-load').empty().removeClass('empty-layouts'),
                view = this;

            $.get(
                ptPbOptions.ajaxurl, {'action': 'ptpb_get_layout'},
                function (result) {
                    var response = JSON.parse(result);
                    if (response.layouts && !response.layouts.hasOwnProperty('length')) {
                        view.dbLayouts = response.layouts;
                        $c.append(view.layoutItems({layouts: response.layouts, type: 'db'}));
                    } else {
                        $c.append(ptPbOptions.i18n.empty_db_layouts).addClass('empty-layouts');
                    }
                    view.initDropDownButtons();
                },
                'html'
            );
        },

        onOpenSaveTab: function (e) {
            this.$('.pt-save-message').empty().removeClass('updated error');
            this.$('#pt-layout-name').val('');
        },

        onOpenImportTab: function (e) {
            this.$('#import-complete').hide();
        }

    });

    ptPbApp.Views.Icons = ptPbApp.Views.Modal.extend({
        template: ptPbApp.template('icon-picker'),
        events: {
            'click .icon-hover a': 'insert',
            'click .close-model': 'triggerCancel',
            'click .close-bbm-modal': 'triggerCancel',
        },
        initialize: function (options) {
            this.select = options.select;
            if(options.callback && typeof options.callback === 'function'){
                this.callback = options.callback;
            }
        },
        insert: function (e) {
            e.preventDefault();

            if(this.callback){
                var rtn = this.callback.call(this, e);
                if(!rtn){
                    return;
                }
            }

            var t = $(e.target),
                icon = t.is('a') ? t : t.parent(),
                $option = this.select.closest('.pt-pb-option-container');

            icon = icon.attr('data-class');

            $option.children('.pt-pb-icon').val(icon).trigger('change');
            $option.children('.icon-preview').html('<i class="fa-5x ' + icon + '"></i>');
            this.triggerCancel();
        },
        attachFilter: function(){
            var view = this,
                active = this.$('.pt-tab-pane.open').attr('id').replace('pt-icons-',''),
                filter = ptPbApp.iconFilter.filters[active];

            filter.set('what','');

            var filterform = new ptPbApp.Views.FilterForm({model: filter}),
                filterView = new ptPbApp.Views.Filter({
                    template: ptPbApp.template('icon-item'),
                    collection: filter.filtered
                });
            this.$('.bbm-modal__topbar').find('form').remove();
            this.$('.bbm-modal__topbar').append(filterform.render().el);    
            this.$('.pt-tab-pane.open').html(filterView.render().el);
        },
        onRender: function () {
            var view = this;
            ptPbApp.Views.Modal.prototype.onRender.call(this);
            this.$('div.pt-tab-pane').on('pt-tab:open', function () {
                view.attachFilter();
            });
        }
    });

    ptPbApp.Views.Actions = Backbone.View.extend({
        template: ptPbApp.template('actions'),
        className: 'pt-pb-actions',
        layout: null,
        events: {
            'click #ptpb_fullscreen': 'toggleFullscreen',
            'click #ptpb_manage_layout': 'layoutManager',
            'click #ptpb_page_options': 'pageOptions',
            'click #ptpb_clear_layout': 'clearLayout',
            'click #ptpb_preview_page': 'previewPage',
            'click #ptpb_save_page': 'savePage',
        },

        render: function (cls) {
            this.$el
                .html(this.template({}));

            $(document).on( 'heartbeat-send.autosave', function( event, data ) { 
                if ( ptPbApp.isPb() && data.wp_autosave ) { 
                    data = _.extend(data, ptPbApp.autoSaveData());
                }
            })

            return this;
        },

        layoutManager: function (e) {
            e && e.preventDefault();
            ptPbApp.app.vent.trigger('ptpb:layouts:show', {model: this.model});
        },

        pageOptions: function (e) {
            e && e.preventDefault();
            ptPbApp.app.vent.trigger('ptpb:options:show', {model: this.model});
        },

        clearLayout: function (e) {
            e && e.preventDefault();
            if (window.confirm(ptPbOptions.i18n.clear_layout)) {
                ptPbApp.clearStage();
            }
        },

        toggleFullscreen: function (e) {
            e && e.preventDefault();
            var $t = $('#ptpb_fullscreen'),
                $b = $('body'),
                $w = $(window),
                mh = $w.height() > 840 ? ($w.height() + 30) : 840,
                $el = $('div#pt-pb-stage'),
                $s = $('#normal-sortables');
            if ($b.hasClass('pt-pb-fullscreen')) {
                $t.text(ptPbOptions.i18n.full_screen);
                $('body').removeClass('pt-pb-fullscreen');
                $el.unwrap();
            } else {
                $t.text(ptPbOptions.i18n.full_screen_exit);
                $el.wrap('<div class="pt-pb-fullscreen-wrap"></div>');
                $el.parent().css({
                    position: 'absolute',
                    left: '0',
                    top: ($el.offset().top) + 'px',
                    width: $s.width() + 'px',
                    zIndex: 9999
                });
                $('body').addClass('pt-pb-fullscreen');
                $el.parent().css({
                    left: '-175px',
                    top: 0,
                    right: 0,
                    width: ($w.width() - 60) + 'px',
                    padding: '30px 25px',
                    minHeight: mh + 'px'
                });
            }
        },

        previewPage: function(e){
            e && e.preventDefault();
            $('#post-preview').trigger('click');
        },

        savePage: function(e){
            e && e.preventDefault();
            $('#publish').trigger('click');
        }

    });

    ptPbApp.Views.Base = Marionette.View.extend({
        $content: null,
        id: function () {
            return this.model.get('id');
        },

        edit: function (e) {
            e && e.preventDefault();
            ptPbApp.app.vent.trigger('ptpb:edit:form', {model: this.model, template: this.editTemplate});
        }

    });

    ptPbApp.Views.Section = ptPbApp.Views.Base.extend({
        template: ptPbApp.template('section'),
        editTemplate: 'section-edit',
        className: 'pt-pb-section grid',

        events: {
            'click .pt-pb-section-toggle': 'toggleSection',
            'click .pt-pb-settings-section': 'edit',
            'click .pt-pb-clone-section': 'cloneSection',
            'click .pt-pb-remove': 'removeSection',
            'click .pt-pb-insert-column': 'insertRow'
        },

        initialize: function (options) {
            this.model.on('remove', this.remove, this);
            this.model.on('change', this.update, this);
            this.model.on('change:label', this.updateLabel, this);
        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()));
            this.$content = this.$('.pt-pb-content');
            this.renderRows();
            return this;
        },

        renderRows: function () {
            this.$content.append(new ptPbApp.Views.Rows({collection: this.model.rows}).render().el);
            return this;
        },

        updateLabel: function (e) {
            this.$('.pt-pb-section-label').text(this.model.get('label'));
        },

        cloneSection: function (e) {
            e && e.preventDefault();
            ptPbApp.Sections.add(_.extend(this.model.toJSON(), {id: null}), {parse: true});
        },

        removeSection: function (e, confirm) {
            e && e.preventDefault();
            if (confirm || window.confirm(ptPbOptions.i18n.remove_section)) {
                this.model.trigger('destroy', this.model);
            }
        },

        toggleSection: function (e) {
            e && e.preventDefault();

            var $this = $(e.target),
                $head = $this.closest('.pt-pb-header'),
                $body = $head.siblings('.pt-pb-content-wrap');

            if ($body.css('display') === undefined || $body.css('display') === 'block') {
                $body.slideUp(400, function () {
                    $head.addClass('close');
                });
            } else {
                $body.slideDown(400, function () {
                    $head.removeClass('close');
                });
            }
        },

        insertRow: function (e) {
            e && e.preventDefault();
            ptPbApp.app.vent.trigger('ptpb:insert:row', {model: this.model, row: false, template: 'insert-row'});
        }

    });

    ptPbApp.Views.Sections = Marionette.CollectionView.extend({
        childView: ptPbApp.Views.Section,
        behaviors: {
            sortable: {
                containment: 'parent',
                behaviorClass: ptPbApp.Behaviors.Sortable
            }
        }
    });

    ptPbApp.Views.Row = ptPbApp.Views.Base.extend({
        template: ptPbApp.template('row'),
        editTemplate: 'row-edit',
        className: 'pt-pb-row clearfix',

        events: {
            'click .pt-pb-settings-row': 'edit',
            'click .pt-pb-settings-columns': 'update',
            'click .pt-pb-remove-row': 'removeRow',
            'click .pt-pb-row-toggle': 'toggleRow',
            'click .pt-pb-clone-row': 'cloneRow'
        },

        initialize: function (options) {
            this.model.on('remove', this.remove, this)
                .on('change:label', this.adminLabel, this);
        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()));

            this.$content = this.$('.pt-pb-row-content');

            this.renderColumns();

            return this;
        },

        renderColumns: function () {
            this.$content.append(new ptPbApp.Views.Columns({collection: this.model.columns}).render().el);
            return this;
        },

        adminLabel: function (model, value) {
            this.$('.pt-pb-row-label').text(value);
        },

        toggleRow: function (e) {
            e && e.preventDefault();

            var $this = $(e.target),
                $head = $this.closest('.pt-pb-row-header'),
                $body = $head.siblings('.pt-pb-row-content');

            if ($body.css('display') === undefined || $body.css('display') === 'block') {
                $body.slideUp(400, function () {
                    $head.addClass('close');
                });
            } else {
                $body.slideDown(400, function () {
                    $head.removeClass('close');
                });
            }
        },

        removeRow: function (e, confirm) {
            e && e.preventDefault();
            if (confirm || window.confirm(ptPbOptions.i18n.remove_row)) {
                this.model.trigger('destroy', this.model);
            }
        },

        cloneRow: function (e, ind) {
            e && e.preventDefault();
            ind = ind || this.model.collection.indexOf(this.model);
            this.model.collection.parent.trigger('ptpb:clone:row', this.model.toJSON(), ind);
        },

        update: function (e) {
            e && e.preventDefault();
            this.model.set('update', true);
            ptPbApp.app.vent.trigger('ptpb:update:row', {model: this.model, row: this.model, template: 'insert-row'});
        }

    });

    ptPbApp.Views.Rows = Marionette.CollectionView.extend({
        childView: ptPbApp.Views.Row,
        className: 'pt-pb-content-inner',
        behaviors: {
            sortable: {
                behaviorClass: ptPbApp.Behaviors.Sortable,
                connectWith: '.pt-pb-content-inner',
                axis: 'y'
            }
        }
    });

    ptPbApp.Views.Column = ptPbApp.Views.Base.extend({
        template: ptPbApp.template('column'),
        editTemplate: 'column-edit',

        className: function () {
            return 'pt-pb-column pt-pb-col-' + this.model.get('type');
        },

        events: {
            'click .save-column': 'save',
            'click .pt-pb-settings-column': 'edit',
            'click .pt-pb-insert-module': 'insert'
        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()));
            this.$content = this.$('.pt-pb-column-content');

            this.renderModules();

            return this;
        },

        renderModules: function () {
            this.$content.append(new ptPbApp.Views.Modules({collection: this.model.modules}).render().el);
            return this;
        },

        insert: function (e) {
            e && e.preventDefault();
            ptPbApp.app.vent.trigger('ptpb:insert:module', {model: this.model, template: 'insert-module'});
        }

    });

    ptPbApp.Views.Columns = Marionette.CollectionView.extend({
        childView: ptPbApp.Views.Column,
        className: 'pt-pb-row-content-inner clearfix',
        behaviors: {
            sortable: {
                behaviorClass: ptPbApp.Behaviors.Sortable,
                containment: 'parent',
                start: function (e, ui) {
                    var col = ui.item.attr('class').replace(/ ?pt-pb-column ?/, '');
                    ui.placeholder.addClass(col).html('<div class="placeholder-inner" style="height:' + ui.item.height() + 'px;width:' + (ui.item.width() - 8) + 'px;"></div>');
                }
            }
        }
    });

    ptPbApp.Views.Module = ptPbApp.Views.Base.extend({
        $content: null,
        id: function () {
            return this.model.get('id');
        },
        className: 'pt-pb-module-preview',

        events: {
            'click .edit-module > .edit': 'edit',
            'click .save-module': 'save',
            'click .edit-module > .remove': 'delete',
            'click .edit-module > .clone': 'clone',
            'click .pt-pb-insert-item': 'insert'
        },

        initialize: function () { 
            this.listenTo(this.model,'remove', this.remove, this);
            this.listenTo(this.model,'change', this.render, this);
            var tmpl = (this.model.get('type') === 'widget' || this.model.get('type') in ptPbOptions.formFields.modules) ? 'module-' + this.model.get('type') : 'no-module';
            this.template = ptPbApp.template(tmpl);
            this.editTemplate = tmpl + '-edit';
        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()));
            this.$content = this.$('input[name="content"]');
            if ((this.model.get('hasItems') && this.model.items) || ( this.model.items && this.model.items.length > 0 )) {
                this.model.items.each(function(item) { 
                    if( ! item.get('type') ){
                      item.set('type', this.model.get('type'));
                    }
                    if( ! item.get('label') ){
                      item.set('label', item.get('title') || 'Item' );
                    }

                }, this);
                this.listenTo(this.model.items,'add remove update change', this.reloadMasonry, this);
                this.$('.content-preview .item-content-wrap').html(new ptPbApp.Views.Items({collection: this.model.items}).render().el);
            }

            var $masonry = this.$('div.masonry-grid');
            $masonry.imagesLoaded( function() {
                // init Masonry after all images have loaded
                $masonry.masonry({ itemSelector: 'div.pt-pb-item-preview' });
            });

            return this;
        },

        delete: function (e, confirm) {
            e && e.preventDefault();
            if (confirm || window.confirm(ptPbOptions.i18n.remove_module)) {
                this.model.trigger('destroy', this.model, this.model.collection);
            }
        },

        clone: function (e, ind) {
            e && e.preventDefault();
            ind = ind || this.model.collection.indexOf(this.model);
            this.model.collection.parent.trigger('ptpb:clone:module', this.model.toJSON(), ++ind);
        },

        insert: function (e) {
            e && e.preventDefault();
            this.model.add({});
        },

        reloadMasonry: function(){
            this.$('div.masonry-grid').masonry( 'reload' );
        }

    });

    ptPbApp.Views.Modules = Marionette.CollectionView.extend({
        childView: ptPbApp.Views.Module,
        className: 'pt-pb-column-content-inner',
        behaviors: {
            sortable: {
                behaviorClass: ptPbApp.Behaviors.Sortable,
                connectWith: '.pt-pb-column-content-inner',
                over: function (event, ui) {
                    ui.item.css('width', (ui.placeholder.closest('.pt-pb-column').width() - 20) + 'px');
                }
            }
        }
    });

    ptPbApp.Views.Item = ptPbApp.Views.Base.extend({
        $content: null,
        id: function () {
            return this.model.get('id');
        },
        className: 'pt-pb-item-preview',

        events: {
            'click .edit-module-item > .edit': 'edit',
            'click .save-module-item': 'save',
            'click .edit-module-item > .remove': 'delete',
            'click .edit-module-item > .clone': 'clone'
        },

        initialize: function () { 
            this.model.on('remove', this.remove, this);
            this.model.on('change', this.render, this);
            var tmpl = 'module-' + this.model.get('type') + '-item';
            this.template = ptPbApp.template(tmpl);
            this.editTemplate = tmpl + '-edit';
        },

        render: function (cls) {
            this.$el.html(this.template(this.model.toJSON()));
            this.$content = this.$('input[name="content"]');

            return this;
        },

        delete: function (e, confirm) {
            e && e.preventDefault();
            if (confirm || window.confirm(ptPbOptions.i18n.remove_module)) {
                this.model.trigger('destroy', this.model, this.model.collection);
            }
        },

        clone: function (e, ind) {
            e && e.preventDefault();
            ind = ind || this.model.collection.indexOf(this.model);
            this.model.collection.parent.trigger('ptpb:clone:item', this.model.toJSON(), ++ind);
        }

    });

    ptPbApp.Views.Items = Marionette.CollectionView.extend({
        childView: ptPbApp.Views.Item,
        className: 'pt-pb-item-content',
        behaviors: {
            sortable: {
                behaviorClass: ptPbApp.Behaviors.Sortable
            }
        }
    });


})(window, Backbone, jQuery, _, ptPbApp);
