App.register('WishlistMailer', function() {
    "use strict";

    var publicMethods = {};

    var $_form;
    var $_element;
    var $_trigger;
    var $_target;

    publicMethods.init = function($element) {
        $_element = $element;
        $_form    = $('form', $element);
        $_target  = $('.message', $element);
        $_trigger = $('a', $element);

        setupListeners();
    };

    var setupListeners = function() {
        $_trigger.on('click', function(event) {
            event.preventDefault();

            $.colorbox({
                top: '25%',
                width: '400px',
                close: Translator.trans('close'),
                overlayClose: true,
                closeButton: true,
                html: $_element.html()
            });
        });

        $_form.on('submit', function(event) {
            $_target.removeClass('msg error');
            var xhr = $.post($_form.attr('action'), $_form.serialize());

            xhr.done(function(response) {
                if (response.status) {
                    $_target.html(response.message);
                    $_form.slideUp();
                    $_target.slideDown();
                } else {
                    $_target.addClass('msg error');
                    $_target.html(response.message);
                }
            });

            xhr.fail(function() {
                console.log(arguments);
            });
        });
    };

    return publicMethods;
});
