/* global Backbone, jQuery, _ */

var ptPbApp = ptPbApp || {};
ptPbApp.Models = ptPbApp.Models || {};

(function (window, Backbone, $, _, ptPbApp) {
    'use strict';

    ptPbApp.Models.Filter = Backbone.Model.extend({
        defaults: {
            what: '', // the textual search
            where: '' // I added a scope to the search
        },
        initialize: function(opts) {
            // the source collection
            this.collection = opts.collection; 
            // the filtered models
            this.filtered = new Backbone.Collection(opts.collection.models); 
            //listening to changes on the filter
            this.on('change:what change:where', this.filter); 
        },

        //recalculate the state of the filtered list
        filter: function() {
            var what = this.get('what').trim(),
                where = this.get('where'),
                lookin = (where==='') ? _.keys(this.collection.models[0].attributes) : where,
                models;
            if (what==='') {
                models = this.collection.models;            
            } else {
                models = this.collection.filter(function(model) {
                    return _.some(_.values(model.pick(lookin)), function(value) {
                        if(typeof value ==='string')
                            return ~value.toLowerCase().indexOf(what.toLowerCase());
                    });
                });
            }

            // let's reset the filtered collection with the appropriate models
            this.filtered.reset(models); 
        }
    });

    ptPbApp.Models.Base = Backbone.Model.extend({
        defaults: {
            f_e: false,

            fh_c: '',
            fh_f: 'inherit', //font_heading_family
            fh_v: 'normal', //font_heading_variant
            fh_s: '32px', //font_heading_size
            fh_lh: '1.5em', //font_heading_lh
            fh_ls: '0', //font_heading_ls
            fh_ws: '0', //font_heading_ws
            fh_b: 0, //font_heading_bold
            fh_i: 0, //font_heading_italic
            fh_u: 0, //font_heading_underline

            ft_c: '',
            ft_f: 'inherit', //font_textfamily
            ft_v: 'normal', //font_textvariant
            ft_s: '13px', //font_textsize
            ft_lh: '1.5em', //font_textlh
            ft_ls: '0', //font_textls
            ft_ws: '0', //font_textws
            ft_b: 0, //font_textbold
            ft_i: 0, //font_textitalic
            ft_u: 0, //font_textunderline

            fh_st: '32px', //Tablets font_heading_size
            ft_st: '13px', //Tablets font_textsize

            fh_sm: '32px', //Mobiles font_heading_size
            ft_sm: '13px', //Mobiles font_textsize

            f_tss: '768;991',
            f_mss: 767
        },
        initialize: function () {
            this.on('change', ptPbApp.updatePBContent);
            this.on('remove', ptPbApp.updatePBContent);
        },
        //override default toJSON method to remove any typography setting if it's not enabled
        toJSON: function (options) {
            var json = Backbone.Model.prototype.toJSON.call(this);
            if (options && options.clean) {
                var opts = ['fh_c', 'fh_f', 'fh_v', 'fh_s', 'fh_lh', 'fh_ls', 'fh_ws', 'fh_b', 'fh_i', 'fh_u', 'ft_c', 'ft_f', 'ft_v', 'ft_s', 'ft_lh', 'ft_ls', 'ft_ws', 'ft_b', 'ft_i', 'ft_u'],
                    del = [];
                if(!json.f_e){
                    del = opts;
                } else {
                    if(json.fh_f == 'inherit') {
                        del = del.concat(opts.slice(1,10));
                    } 
                    if(json.ft_f == 'inherit'){
                        del = del.concat(opts.slice(11));
                    }
                }

                if(!json.f_et){
                    del = del.concat(['f_tss','fh_st','ft_st']);
                }

                if(!json.f_em){
                    del = del.concat(['f_mss','fh_sm','ft_sm']);
                }
                _.each(del, function (opt, i) {
                    if (opt in json)
                        delete json[opt];
                });
            } 
            return json;
        }
    });

    ptPbApp.Models.PageOptions = ptPbApp.Models.Base.extend({
        defaults: function () {
            return _.extend({
                'fullwidth': 'no',
                'layout': 'default'
            }, ptPbApp.Models.Base.prototype.defaults);
        },
        initialize: function () {
            this.on('change', ptPbApp.updatePBOptions);
            this.on('remove', ptPbApp.updatePBOptions);
        },
    });

    ptPbApp.Models.Section = ptPbApp.Models.Base.extend({

        defaults: function () {
            return _.extend({
                id: '',
                css_class: '',
                pt: '30px',
                pb: '30px',
                btw: '0px',
                bbw: '0px',
                btc: '',
                bbc: ''
            }, 
            ptPbOptions.formFields.section,
            ptPbApp.Models.Base.prototype.defaults);
        },

        initialize: function () {
            ptPbApp.Models.Base.prototype.initialize.call(this);
            _.defaults(this, {
                rows: new ptPbApp.Collections.Row([], {parent: this}),
                rowNum: 1
            });

            if (!this.get('id'))
                this.set('id', ptPbApp.getSectionNum());

            this.on('ptpb:add:row', function (row, ind) {
                this.add(row, {at: ind});
            });
            this.on('ptpb:clone:row', function (row, ind) {
                this.add(row, {at: ind});
            });
        },

        add: function (attr, options) {
            var id = this.getRowId();
            return this.rows.add(_.extend(attr, {'id': id}), _.extend({parse: true}, options || {}));
        },

        parse: function (response) {
            var section = this;
            this.rowNum = 1;
            response.id = ptPbApp.getSectionNum();
            if (_.has(response, "rows")) {
                response.rows = _.isObject(response.rows) ? _.map(response.rows, function(v,k){return v;}) : response.rows;
                response.rows = response.rows.map(function (row) {
                    row.id = section.getRowId(response.id);
                    return row;
                });
                this.rows = new ptPbApp.Collections.Row(response.rows, {
                    parse: true,
                    parent: this
                });
            }
            return response;
        },

        toJSON: function (options) {
            var json = ptPbApp.Models.Base.prototype.toJSON.call(this, options);
            json.rows = this.rows.toJSON(options);
            return json;
        },

        getRowId: function (id) {
            return (id || this.get('id')) + '_r' + this.rowNum++;
        }

    });

    ptPbApp.Models.Row = ptPbApp.Models.Base.extend({

        defaults: function () {
            return _.extend({
                id: '',
                pt: '0px',
                pb: '0px',
                pl: '0px',
                pr: '0px',
            }, 
            ptPbOptions.formFields.row,
            ptPbApp.Models.Base.prototype.defaults)
        },

        initialize: function () {
            ptPbApp.Models.Base.prototype.initialize.call(this);
            _.defaults(this, {
                columns: new ptPbApp.Collections.Column([], {parent: this}),
                colNum: 1
            });

            this.on('ptpb:update:row', this.updateColumns);

            if (this.get('layout')) {
                var model = this;
                _.each(this.get('layout'), function (col) {
                    model.add({type: col});
                });
                this.unset('layout');
            }
        },

        add: function (attr) {
            return this.columns.add(_.extend(attr, {id: this.getColumnId()}));
        },

        updateColumns: function (options) {
            var row = this.toJSON(),
                columns = options.layout.map(function (layout, i) {
                    return _.extend(row.columns[i] || {}, {type: layout});
                });
            this.columns.reset(columns, {parse: true});
        },

        parse: function (response) {
            var row = this;
            this.colNum = 1;
            if (_.has(response, "columns")) {
                response.columns = _.isObject(response.columns) ? _.map(response.columns, function(v,k){return v;}) : response.columns;
                response.columns = response.columns.map(function (column) {
                    column.id = row.getColumnId(response.id);
                    return column;
                });
                this.columns = new ptPbApp.Collections.Column(response.columns, {
                    parse: true,
                    parent: this
                });
            }
            return response;
        },

        toJSON: function (options) {
            var json = ptPbApp.Models.Base.prototype.toJSON.call(this, options);
            json.columns = this.columns.toJSON(options);
            return json;
        },

        getColumnId: function (id) {
            return (id || this.get('id')) + '_c' + this.colNum++;
        }

    });

    ptPbApp.Models.Column = ptPbApp.Models.Base.extend({

        defaults: function () {
            return _.extend({
                id: '',
                type: '1-1',
                pl: '15px',
                pr: '15px',
                pt: '0px',
                pb: '0px',
                blw: '0px',
                brw: '0px',
                blc: '',
                brc: '',
                css_class: ''
            }, 
            ptPbOptions.formFields.column,
            ptPbApp.Models.Base.prototype.defaults);
        },

        initialize: function () {
            ptPbApp.Models.Base.prototype.initialize.call(this);
            _.defaults(this, {
                modules: new ptPbApp.Collections.Module([], {parent: this}),
                modNum: 1
            });

            this.on('ptpb:add:module', function (module, ind) {
                this.add(module, {at: ind});
            });
            this.on('ptpb:clone:module', function (module, ind) {
                this.add(module, {at: ind});
            });
        },

        add: function (attr, options) {
            var column = this;
            return this.modules.add(_.extend(
                {},
                column.getModuleDefaults(attr),
                attr, {
                    id: this.getModuleId(),
                    type: attr.module,
                    widget: attr.widget || false
                }), _.extend({parse: true}, options || {}));
        },

        getModuleDefaults: function(attr){
            if(attr && attr.module === 'widget'){
                return ptPbOptions.widgets[attr.widget] || {};
            }
            return ptPbOptions.formFields.modules[attr.module] || {};
        },

        parse: function (response) {
            var column = this;
            this.modNum = 1;
            if (_.has(response, "modules")) {
                response.modules = _.isObject(response.modules) ? _.map(response.modules, function(v,k){return v;}) : response.modules;
                response.modules = response.modules.map(function (module) {
                    return _.extend({}, column.getModuleDefaults(module), module, {id: column.getModuleId(response.id)});
                });
                this.modules = new ptPbApp.Collections.Module(response.modules, {
                    parse: true,
                    parent: this
                });
            }
            return response;
        },

        toJSON: function (options) {
            var json = ptPbApp.Models.Base.prototype.toJSON.call(this, options);
            json.modules = this.modules.toJSON(options);
            return json;
        },

        getModuleId: function (id) {
            return (id || this.get('id')) + '_m' + this.modNum++;
        }

    });

    ptPbApp.Models.Module = ptPbApp.Models.Base.extend({
        defaults: function () {
            return _.extend({
                'mb': '20px', //margin_bottom
                'pt': '0px', //padding_top
                'pb': '0px', //padding_bottom
                'pl': '0px', //padding_left
                'pr': '0px', //padding_right
                'animation': '',
            }, ptPbApp.Models.Base.prototype.defaults);
        },

        initialize: function () {
            ptPbApp.Models.Base.prototype.initialize.call(this);
            _.defaults(this, {
                items: new ptPbApp.Collections.Item([], {parent: this}),
                itemNum: 1
            });
            this.on('ptpb:clone:item', function (item, ind) {
                this.add(item, {at: ind});
            });
        },

        getItemDefaults: function(attr){
            return ptPbOptions.formFields.items[attr || this.get('type')] || {};
        },

        toJSON: function (options) {
            var json = ptPbApp.Models.Base.prototype.toJSON.call(this, options);
            if (this.get('hasItems'))
                json.items = this.items.toJSON(options);
            return json;
        },

        parse: function (response) {
            var module = this,
                typ = module.get('item');
            this.itemNum = 1;
            if (_.has(response, "items")) {
                response.items = _.isObject(response.items) ? _.map(response.items, function(v,k){return v;}) : response.items;
                response.items = response.items.map(function (item) {
                    return _.extend({}, module.getItemDefaults(typ), item, {id: module.getItemId(response.id), type: typ});
                });
                this.items = new ptPbApp.Collections.Item(response.items, {
                    parent: this
                });
            }
            return response;
        },

        getItemId: function (id) {
            return (id || this.get('id')) + '_i' + this.itemNum++;
        },

        add: function (attr, options) {
            return this.items.add(_.extend(
                this.getItemDefaults(),
                attr,
                {
                    id: this.getItemId(),
                    type: this.get('type')
                }), options || {});
        },

    });

})(window, Backbone, jQuery, _, ptPbApp);
