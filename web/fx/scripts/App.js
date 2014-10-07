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
            var $element   = $(module);
            var moduleName = $element.data('moduleName').split(' ');

            $.each(moduleName, function(i, name) {
                if (undefined === publicMethods[name]) {
                    throw 'Unknown name "'+name+'" called.';
                }

                if (typeof publicMethods[name].init === 'function') {
                    publicMethods[name].init($element);
                }
            });
        });
    };

    return publicMethods;
})(jQuery);
