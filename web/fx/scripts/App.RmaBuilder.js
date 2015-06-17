/**
 * We hook into the ProductFinder to handle lokups and stock checks.
 * Here we just toggles the right elements in the form.
 */
App.register('RmaBuilder', function() {
    "use strict";

    var publicMethods = {};

    var $_element,
        identifiers;

    publicMethods.init = function($element) {
        $_element     = $element;

        identifiers = {
            form          : 'form.rma-form',
            searchField   : 'input[name="q"]',
            masterField   : 'input[name="master"]',
            productIdField: 'input[name="product_id"]',
            sizeSelect    : 'select[name="size"]',
            colorSelect   : 'select[name="color"]',
            quantitySelect: 'select[name="quantity"]',
            resetButton   : 'input.reset'
        };

        setupListeners();
    };

    var setupListeners = function() {

        // when products are found update the visibility and focus of the dropdowns
        $_element.on('on-products-found', function(event, data) {

            var $scope       = data.scope,
                $form        = $scope.parents(identifiers.form),
                $sizeSelect  = $(identifiers.sizeSelect, $form),
                $colorSelect = $(identifiers.colorSelect, $form);

            switch (data.target) {
                case 'size':
                    $sizeSelect.parent().show();
                    $sizeSelect.focus();
                    break;
                case 'color':
                    $colorSelect.parent().show();
                    $colorSelect.focus();
                    break;
            }

            $('input.reset', $form).show();
        });

        // reset the form when reset is clicked.
        $_element.find(identifiers.resetButton).on('click', function () {

            var $scope       = $(this),
                $form        = $scope.parents(identifiers.form),
                $sizeSelect  = $(identifiers.sizeSelect, $form),
                $colorSelect = $(identifiers.colorSelect, $form),
                $resetButton = $(identifiers.resetButton, $form);

            $('.rma-productreplacement option:not(":first")', $form).remove();
            $sizeSelect.parent().hide();
            $colorSelect.parent().hide();
            $resetButton.hide();
            App.ProductFinder.resetForm($form);
        });
    };

    return publicMethods;
});
