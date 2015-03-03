App.register('WishlistBuilder', function() {
    "use strict";

    var publicMethods = {};

    var $_form;
    var $_element;
    var $_target;
    var $_searchField;
    var $_masterField;
    var $_resetter;

    publicMethods.init = function($element) {
        $_element     = $element;
        $_form        = $('form', $element);
        $_target      = $('.js-wishlist-target');
        $_searchField = $('input[name="q"]', $_form);
        $_masterField = $('input[name="master"]', $_form);
        $_resetter    = $('.js-wishlist-flush-list', $_target);

        setupListeners();
        yatzy.compile('wishlistItemTpl');
    };

    var setupListeners = function() {
        $_form.on('submit', function(event) {
            event.preventDefault();


            var xhr = $.post($_form.attr('action'), $_form.serialize()),
                $form = $(this).parents('form');

            xhr.done(function(response) {
                if ($('#js-wishlist-'+response.data.id, $_target).length) {
                    var $product = $('#js-wishlist-'+response.data.id, $_target);

                    $('.js-in-edit', $_target).removeClass('js-in-edit');

                    $product.data('quantity', response.data.quantity);
                    $('span', $product).text(response.data.quantity);
                } else {
                    $_target.prepend(yatzy.render('wishlistItemTpl', response.data));

                    $('.js-in-edit', $_target).fadeOut(function() {
                       $(this).remove();
                    });
                }

                App.ProductFinder.resetForm($form);

                // show resetter link and shoppinglist number below list when not empty.
                $_resetter.removeClass('off');
                $('.list-number.last').removeClass('off');
                $_searchField.focus();
            });

            xhr.fail(function() {
                console.log(arguments);
            });
        });

        $_resetter.on('click', function(event) {
            event.preventDefault();
            var href = this.href;

            dialoug.confirm('OBS !', $(this).data('confirmMessage'), function(state) {
                if (state == 'ok') {
                    $.post(href);

                    $('article', $_target).remove();
                    $_resetter.addClass('off');
                    $('.list-number.last').addClass('off');
                }
            });
        });

        $(document).on('click', 'a.js-wishlist-edit-item-trigger', function(event) {
            event.preventDefault();

            var $article    = $(this).closest('article'),
                bailOnReset = false,
                $scope      = $(this);

            if ($article.hasClass('js-in-edit')) {
                bailOnReset = true;
            }

            $('article', $_target).removeClass('js-in-edit');
            App.ProductFinder.resetForm($_form);

            if (bailOnReset) {
                return;
            }

            $article.addClass('js-in-edit');

            var data = $article.data();

            $_masterField.val(data.master);
            $_searchField.val(data.title);
            App.ProductFinder.stockCheck({
                master : data.master
            }, 'size', $scope);

            $('html,body').animate({
                scrollTop: $_searchField.offset().top - 50
            });
        });

        $(document).on('click', '.js-wishlist-delete-item-trigger', function(event) {
            event.preventDefault();

            $.post(this.href);
            $(this).closest('article').remove();
        });
    };

    return publicMethods;
});
