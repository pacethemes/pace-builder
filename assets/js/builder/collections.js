/* global Backbone, jQuery, _ */
var ptPbApp = ptPbApp || {};
ptPbApp.Collections = ptPbApp.Collections || {};

(function (window, Backbone, $, _, ptPbApp) {
    'use strict';

    ptPbApp.Collections.Base = Backbone.Collection.extend({
        initialize: function (models, options) {
            this.on("change reset add remove update sort", ptPbApp.updatePBContent);
            this.parent = (options && options.parent) ? options.parent : null;
        }
    });

    ptPbApp.Collections.Section = Backbone.Collection.extend({
        model: ptPbApp.Models.Section,
        initialize: function (models, options) {
            this.on("change reset add remove update sort", ptPbApp.updatePBContent);
        }
    });

    ptPbApp.Collections.Row = ptPbApp.Collections.Base.extend({
        model: ptPbApp.Models.Row
    });

    ptPbApp.Collections.Column = ptPbApp.Collections.Base.extend({
        model: ptPbApp.Models.Column
    });

    ptPbApp.Collections.Module = ptPbApp.Collections.Base.extend({
        model: ptPbApp.Models.Module
    });

    ptPbApp.Collections.Item = ptPbApp.Collections.Base.extend({
    });

})(window, Backbone, jQuery, _, ptPbApp);
