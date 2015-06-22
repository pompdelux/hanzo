App.register('WishlistBuilder', function() {
    "use strict";

    var publicMethods = {};

    var $_element,
        identifiers,
        $_target,
        $_resetter,
        $_form,
        $_masterField,
        $_searchField;

    publicMethods.init = function($element) {
        $_element     = $element;
        identifiers = {
            form           : 'form.wishlist',
            target         : '.js-wishlist-target',
            resetter       : '.js-wishlist-flush-list',
            searchField    : 'input[name="q"]',
            masterField    : 'input[name="master"]'
        };
        $_target      = $(identifiers.target);
        $_resetter    = $(identifiers.resetter, $_target);
        $_form        = $(identifiers.form),
        $_masterField = $(identifiers.masterField, $_form),
        $_searchField = $(identifiers.searchField, $_form);

        setupListeners();
        yatzy.compile('wishlistItemTpl');
    };

    var setupListeners = function() {

        // submit
        $_form.on('submit', function(event) {
            event.preventDefault();

            var $form = $(this),
                xhr = $.post($form.attr('action'), $form.serialize());

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

                App.ProductFinder.resetForm($_form);

                // show resetter link and shoppinglist number below lis.t when not empty
                $_resetter.removeClass('off');
                $('.list-number.last').removeClass('off');
                $_searchField.focus();
            });

            xhr.fail(function() {
                console.log(arguments);
            });
        });

        // reset
        $_resetter.on('click', function(event) {
            event.preventDefault();
            var $scope = $(this),
                href = this.href;

            dialoug.confirm('OBS !', $(this).data('confirmMessage'), function(state) {
                if (state == 'ok') {
                    $.post(href);

                    $('article', $_target).remove();
                    $scope.addClass('off');
                    $('.list-number.last').addClass('off');
                }
            });
        });

        // edit
        $(document).on('click', '.js-wishlist-edit-item-trigger', function(event) {
            event.preventDefault();

            var $scope      = $(this),
                $article    = $scope.parents('article'),
                bailOnReset = false;

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
            }, 'size', $_searchField);

            $('html,body').animate({
                scrollTop: $_searchField.offset().top - 50
            });
        });

        // delete
        $(document).on('click', '.js-wishlist-delete-item-trigger', function(event) {
            event.preventDefault();

            $.post(this.href);
            $(this).closest('article').remove();
        });
    };

    return publicMethods;
});
