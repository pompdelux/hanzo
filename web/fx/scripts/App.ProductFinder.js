/* global App:false, Translator:false, dialoug:false, base_url:false */
App.register('ProductFinder', function () {
    "use strict";

    var publicMethods = {};

    // private jquery elements.
    var $_element,
        identifiers;

    /**
     * Initirat
     * @param $element
     */
    publicMethods.init = function ($element) {
        $_element = $element;

        identifiers = {
            form          : 'form.rma-form',
            searchField   : 'input[name="q"]',
            masterField   : 'input[name="master"]',
            productIdField: 'input[name="product_id"]',
            sizeSelect    : 'select[name="size"]',
            colorSelect   : 'select[name="color"]',
            quantitySelect: 'select[name="quantity"]'
        };

        setupSearch();
        setupListeners();
    };

    /**
     * Reset the form elements this module cares about.
     */
    publicMethods.resetForm = function ($scope) {

        var $form_object;

        if ($scope.is('form')) {
            $form_object = $scope;
        }
        else {
            $form_object = $scope.parents(identifiers.form);
        }

        var $sizeSelect_object = $(identifiers.sizeSelect, $form_object),
            $colorSelect_object = $(identifiers.colorSelect, $form_object),
            $quantitySelect_object = $(identifiers.quantitySelect, $form_object),
            $ttdd = $('.tt-dropdown-menu', $form_object);

        $ttdd.html('');
        $ttdd.css('display', 'none');

        // Empty fields
        $(identifiers.searchField).val('');
        $(identifiers.masterField).val('');
        $(identifiers.productIdField).val('');

        // Size
        $sizeSelect_object.prop('disabled', true);
        $('option:first', $sizeSelect_object).prop('selected', true);

        // Color
        $colorSelect_object.prop('disabled', true);
        $('option:first', $colorSelect_object).prop('selected', true);

        // Quantity
        $quantitySelect_object.prop('disabled', true);
        $('option:first', $quantitySelect_object).prop('selected', true);

    };


    /**
     * Setup the search form
     */
    var setupSearch = function () {

        var $searchField_object = $(identifiers.searchField, $_element);

        // Setup typeahead search
        $searchField_object.typeahead({
            remote: {
                url       : base_url + "quickorder/get-sku?name=%QUERY",
                beforeSend: function (jqXHR, settings) {
                    var query = settings.url.split('?')[1];
                    var params = {};
                    $.each(query.split('&'), function (index, element) {
                        var x = element.split('=');
                        params[x[0]] = x[1];
                    });

                    if ((typeof params.name === "undefined") ||
                        (params.name.length < 3) ||
                        (params.name.indexOf(' ') !== -1)
                    ) {
                        return false;
                    }
                }
            }
        });

        // Event
        $searchField_object.on('typeahead:autocompleted typeahead:selected', function (event, item) {

            var $scope = $(this),
                $form_object = $scope.parents(identifiers.form),
                $masterField_object = $(identifiers.masterField, $form_object);

            $masterField_object.val(item.name);

            publicMethods.stockCheck({master: item.name}, 'size');

        });
    };

    /**
     * Setup listeners
     */
    var setupListeners = function () {

        // handle found products ...
        $_element.on('on-products-found', function (event, data) {
            var $target_object,
                label,
                $form_object = $(this).find(identifiers.form),
                $sizeSelect_object = $(identifiers.sizeSelect, $form_object),
                $colorSelect_object = $(identifiers.colorSelect, $form_object);

            switch (data.target) {
                case 'size':
                    $target_object = $sizeSelect_object;
                    label = Translator.trans('wishlist.select.size');
                    break;
                case 'color':
                    $target_object = $colorSelect_object;
                    label = Translator.trans('wishlist.select.color');
                    break;
            }

            $('option', $target_object).remove();

            $target_object.append('<option value="">' + label + '</option>');
            $target_object.prop("disabled", false);

            $.each(data.data.products, function (key, value) {

                // we need this to filter out dubbs
                if ($('option[value="' + value[data.target] + '"]').length) {
                    return;
                }

                var label = value[data.target];

                if ('size' === data.target) {
                    label = value.size_label || value[data.target];
                }

                $target_object.append('<option value="' + value[data.target] + '" data-master="' + value.master + '" data-product-id="' + value.product_id + '">' + label + '</option>');
            });

            $target_object.focus();
        });

        // handle not-found cases
        $_element.on('on-no-products-found', function (event, data) {
            console.log(data);
        });

        // Size select
        // look up colors from a master and a size
        $(identifiers.sizeSelect, $_element).on('change', function () {
            var $scope = $(this),
                $form_object = $scope.parents(identifiers.form),
                $masterField_object = $(identifiers.masterField, $form_object),
                $sizeSelect_object = $(identifiers.sizeSelect, $form_object);

            publicMethods.stockCheck({
                master: $masterField_object.val(),
                size  : $sizeSelect_object.val()
            }, 'color');
        });

        // set field state and focus
        $(identifiers.colorSelect, $_element).on('change', function () {
            var $scope = $(this),
                $form_object = $scope.parents(identifiers.form),
                $quantitySelect_object = $(identifiers.quantitySelect, $form_object),
                $productIdField_object = $(identifiers.productIdField, $form_object);

            $productIdField_object.val($(':selected', $scope).data('productId'));
            $quantitySelect_object.prop('disabled', false).focus();
        });
    };

    /**
     * Perform the stockcheck
     *
     * @param data
     * @param target
     */
    publicMethods.stockCheck = function (data, target) {
        var xhr = $.ajax({
            url     : base_url + "stock-check",
            dataType: 'json',
            type    : 'GET',
            data    : data,
            cache   : false,
            async   : false
        });

        xhr.done(function (response) {
            response.target = target;
            if (response.status) {
                return $_element.trigger('on-products-found', response);
            }

            $_element.trigger('on-no-products-found', response);
        });

        xhr.fail(function () {
            dialoug.error(Translator.trans('notice'), Translator.trans('an.error.occurred'));
        });
    };

    return publicMethods;
});
