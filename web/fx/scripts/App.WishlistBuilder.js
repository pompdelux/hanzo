App.register('WishlistBuilder', function() {
    "use strict";

    var publicMethods = {};

    var $_form;

    publicMethods.init = function($element) {
        $_form = $('form', $element);

        setupListeners();
    };

    var setupListeners = function() {
        $_form.on('submit', function(event) {
            event.preventDefault();

            var xhr = $.post($_form.attr('action'), $_form.serialize());

            xhr.done(function(response) {
                console.log(response);
            });

            xhr.fail(function() {
                console.log(arguments);
            });
        });
    };

    return publicMethods;
});
