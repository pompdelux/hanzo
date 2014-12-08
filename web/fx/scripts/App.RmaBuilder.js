/**
 * We hook into the ProductFinder to handle lokups and stock checks.
 * Here we just toggles the right elements in the form.
 */
App.register('RmaBuilder', function() {
    "use strict";

    var publicMethods = {};

    var $_form;
    var $_element;
    var $_sizeSelect;
    var $_colorSelect;
    var $_resetButton;

    publicMethods.init = function($element) {
        $_element     = $element;
        $_form        = $('form', $element);
        $_sizeSelect  = $('select[name="size"]', $_form);
        $_colorSelect = $('select[name="color"]', $_form);
        $_resetButton = $('input.reset', $_form);

        setupListeners();
    };

    var setupListeners = function() {
        // when products are found update the visibility and focus of the dropdowns
        $_element.on('on-products-found', function(event, data) {
            switch (data.target) {
                case 'size':
                    $_sizeSelect.parent().show();
                    $_sizeSelect.focus();
                    break;
                case 'color':
                    $_colorSelect.parent().show();
                    $_colorSelect.focus();
                    break;
            }

            $('input.reset', $_form).show();
        });

        // reset the form when reset is clicked.
        $_resetButton.on('click', function () {
            $('.rma-productreplacement option:not(":first")', $_form).remove();
            $_sizeSelect.parent().hide();
            $_colorSelect.parent().hide();
            $_resetButton.hide();
            App.ProductFinder.resetForm();
        });
    };

    return publicMethods;
});
