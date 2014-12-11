App.register('WishlistMailer', function() {
    "use strict";

    var publicMethods = {};

    var $_form;
    var $_element;
    var $_trigger;
    var $_target;
    var $_dialoug;

    publicMethods.init = function($element) {
        $_element = $element;
        $_trigger = $('a.js-email-trigger', $element);
        $_dialoug = $('#js-send-wishlist');

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
                html: $_dialoug.html()
            });

            // we need to rescan the content to get the right elements
            // they are injected into the colorbox element, and their initial location is therefor invalid
            var $cb  = $('#cboxLoadedContent');
            $_form   =  $('form', $cb);
            $_target = $('.message', $cb);

            $_form.on('submit', function(event) {
                event.preventDefault();

                $_target.addClass('off').removeClass('msg error');
                var xhr = $.post($_form.attr('action'), $_form.serialize());

                xhr.done(function(response) {
                    if (response.status) {
                        $_target.html(response.message);
                        $_form.addClass('off');
                        $_target.removeClass('off');
                    } else {
                        $_target.html(response.message);
                        $_target.removeClass('off').addClass('msg error');
                    }

                    $.colorbox.resize();
                });

                xhr.fail(function() {
                    console.log(arguments);
                });
            });
        });
    };

    return publicMethods;
});
