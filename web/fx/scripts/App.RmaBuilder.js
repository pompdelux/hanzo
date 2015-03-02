/* global App:false
 * We hook into the ProductFinder to handle lokups and stock checks.
 * Here we just toggles the right elements in the form.
 */
App.register('RmaBuilder', function () {
    "use strict";

    var publicMethods = {};

    var $_element;
    var $_identifiers;

    publicMethods.init = function ($element) {
        $_element = $element;
        $_identifiers = {
            sizeSelect : 'select[name="size"]',
            colorSelect: 'select[name="color"]',
            resetButton: 'input.reset'
        };

        setupListeners();
    };

    var setupListeners = function () {
        // when products are found update the visibility and focus of the dropdowns
        $_element.on('on-products-found', function (event, data) {
            var $scope = data.scope,
                $form_object = $scope.parents('form'),
                $sizeSelect_object = $($_identifiers.sizeSelect, $form_object),
                $colorSelect_object = $($_identifiers.colorSelect, $form_object),
                $resetButton_object = $($_identifiers.resetButton, $form_object);

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
        $($_identifiers.resetButton).on('click', function () {

            var $scope = $(this),
                $form_object = $scope.parents('form'),
                $sizeSelect_object = $($_identifiers.sizeSelect, $form_object),
                $colorSelect_object = $($_identifiers.colorSelect, $form_object),
                $resetButton_object = $($_identifiers.resetButton, $form_object);

            $('.rma-productreplacement option:not(":first")', $form_object).remove();
            $sizeSelect_object.parent().hide();
            $colorSelect_object.parent().hide();
            $resetButton_object.hide();
            App.ProductFinder.resetForm($scope);
        });
    };

    return publicMethods;
});
