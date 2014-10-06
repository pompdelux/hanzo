/**
 * App is the main application.
 *
 * @param {jQuery} $ jQuery object
 *
 * @return {App.publicMethods}
 */
var App = (function($) {
    "use strict";

    /**
     * Public method holder
     *
     * @type {Object}
     */
    var publicMethods = {};

    /**
     * Register new module
     *
     * @param {string}   name   - Name for the module.
     * @param {function} module - The actual module code.
     */
    publicMethods.register = function(name, module) {
        publicMethods[name] = (module)();
    };

    /**
     * Initialize the App - must happend after all other js is loaded.
     */
    publicMethods.init = function() {
        $('.js-hanzo-module').each(function(i, module) {
            var $module    = $(module);
            var moduleName = $module.data('moduleName');

            if (undefined === publicMethods[moduleName]) {
                throw 'Unknown moduleName "'+moduleName+'" called.';
            }

            if (typeof publicMethods[moduleName].init === 'function') {
                publicMethods[moduleName].init($module);
            }
        });
    };

    return publicMethods;
})(jQuery);
