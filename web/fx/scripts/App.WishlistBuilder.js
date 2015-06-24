/* global accounting:true, App:true, yatzy:true */
App.register('WishlistBuilder', function() {
    "use strict";

    var publicMethods = {};

    var $_element,
        $_target,
        $_resetter,
        $_form,
        $_masterField,
        $_searchField,
        $_total,
        $_actionField,
        $_oldProductIdField;

    publicMethods.init = function($element) {
        $_element     = $element;
        $_target      = $('.js-wishlist-target');
        $_resetter    = $('.js-wishlist-flush-list', $_target);

        $_form              = $('form.wishlist');
        $_masterField       = $('input[name="master"]', $_form);
        $_searchField       = $('input[name="q"]', $_form);
        $_actionField       = $('input[name="action"]', $_form);
        $_oldProductIdField = $('input[name="old_product_id"]', $_form);

        $_total       = $('.js-wishlist-total');

        setupListeners();
        yatzy.compile('wishlistItemTpl');
    };

    function updateTotal(total) {
      $_total.text(total);
    }

    function setActionAdd() {
      $_actionField.val('add');
    }

    function setActionEdit() {
      $_actionField.val('edit');
    }

    function setOldProductId(id) {
      $_oldProductIdField.val(id);
    }

    function resetOldProductId() {
      $_oldProductIdField.val("");
    }

    var setupListeners = function() {

        // submit
        $_form.on('submit', function(event) {
            event.preventDefault();

            var $form = $(this),
                xhr = $.post($form.attr('action'), $form.serialize());

            xhr.done(function(response) {

                var $product = $('#js-wishlist-'+response.data.id, $_target);
                if ($product.length) {
                    $('.js-in-edit', $_target).removeClass('js-in-edit');

                    $product.data('quantity', response.data.quantity);
                    $('span.quantity', $product).text(response.data.quantity);
                } else {
                    $_target.prepend(yatzy.render('wishlistItemTpl', response.data));

                    $('.js-in-edit', $_target).fadeOut(function() {
                       $(this).remove();
                    });
                }

                updateTotal(response.total_price);
                setActionAdd();
                resetOldProductId();

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
                    updateTotal(0);
                }
            });
        });

        // edit
        $(document).on('click', '.js-wishlist-edit-item-trigger', function(event) {
            event.preventDefault();
            setActionEdit();

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
            setOldProductId(data.productId);

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

            var xhr = $.post(this.href);
            $(this).closest('article').remove();

            xhr.done(function(response) {
              updateTotal(response.total_price);
            });
        });
    };

    return publicMethods;
});
