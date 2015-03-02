/* global App:false
 * We hook into the ProductFinder to handle lokups and stock checks.
 * Here we just toggles the right elements in the form.
 */
App.register('RmaBuilder', function () {
    "use strict";

    var publicMethods = {};

    var $_element;
    var identifiers;

    publicMethods.init = function ($element) {
        $_element = $element;
        identifiers = {
            form       : 'form.rma-form',
            sizeSelect : 'select[name="size"]',
            colorSelect: 'select[name="color"]',
            resetButton: 'input.reset'
        };

        setupListeners();
    };

    var setupListeners = function () {

        // when products are found update the visibility and focus of the dropdowns
        $_element.on('on-products-found', function (event, data) {

            var $sizeSelect_object = $(identifiers.sizeSelect, $_element),
                $colorSelect_object = $(identifiers.colorSelect, $_element),
                $resetButton_object = $(identifiers.resetButton, $_element);

            console.log($sizeSelect_object);

            switch (data.target) {
                case 'size':
                    $sizeSelect_object.parent().show();
                    $sizeSelect_object.focus();
                    break;
                case 'color':
                    $colorSelect_object.parent().show();
                    $colorSelect_object.focus();
                    break;
            }

            $resetButton_object.show();
        });

        // reset the form when reset is clicked.
        $(identifiers.resetButton, $_element).on('click', function () {

            var $sizeSelect_object = $(identifiers.sizeSelect, $_element),
                $colorSelect_object = $(identifiers.colorSelect, $_element),
                $resetButton_object = $(identifiers.resetButton, $_element);

            $('.rma-productreplacement option:not(":first")', $_element).remove();
            $sizeSelect_object.parent().hide();
            $colorSelect_object.parent().hide();
            $resetButton_object.hide();
            App.ProductFinder.resetForm($(this));
        });
    };

    return publicMethods;
});
