App.register('ProductFinder', function() {
    "use strict";

    var publicMethods = {};

    // private jquery elements.
    var $_element;
    var $_identifiers;

    /**
     * Initirat
     * @param $element
     */
    publicMethods.init = function($element) {
        $_element = $element;

        $_identifiers = {
            searchField: 'input[name="q"]',
            masterField: 'input[name="master"]',
            productIdField: 'input[name="product_id"]',
            sizeSelect: 'select[name="size"]',
            colorSelect: 'select[name="color"]',
            quantitySelect: 'select[name="quantity"]'
        };

        setupSearch();
        setupListeners();
    };

    /**
     * Reset the form elements this module cares about.
     */
    publicMethods.resetForm = function($scope) {

        var $form_object

        if($scope.is('form')) {
            console.log('is form');
            $form_object = $scope;
        }
        else {
            console.log('not form');
            $form_object = $scope.parents('form');
        }

        var $sizeSelect_object = $($_identifiers.sizeSelect, $form_object),
            $colorSelect_object = $($_identifiers.colorSelect, $form_object),
            $quantitySelect_object = $($_identifiers.quantitySelect, $form_object),
            $ttdd = $('.tt-dropdown-menu', $form_object);

        $ttdd.html('');
        $ttdd.css('display', 'none');

        $($_identifiers.searchField).val('');
        $($_identifiers.masterField).val('');
        $($_identifiers.productIdField).val('');

        $sizeSelect_object.prop('disabled', true);
        $('option:first', $sizeSelect_object).prop('selected', true);

        $colorSelect_object.prop('disabled', true);
        $('option:first', $colorSelect_object).prop('selected', true);

        $quantitySelect_object.prop('disabled', true);
        $('option:first', $quantitySelect_object).prop('selected', true);
    };


    /**
     * Setup the search form
     */
    var setupSearch = function() {

        // setup typeahead search
        $($_identifiers.searchField).typeahead({
            name   : "sku",
            remote : {
                url: base_url + "quickorder/get-sku?name=%QUERY",
                beforeSend : function(jqXHR, settings) {
                    var query = settings.url.split('?')[1];
                    var params = {};
                    $.each(query.split('&'), function(index, element) {
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
    };

    /**
     * Setup listeners
     */
    var setupListeners = function() {

        // handle typeahead requests
        $($_identifiers.searchField).on('typeahead:autocompleted typeahead:selected', function(event, item) {
            var $scope = $(this),
                $form_object = $scope.parents('form'),
                $masterField_object = $($_identifiers.masterField, $form_object);

            console.log(item.name);
            $masterField_object.val(item.name);
            publicMethods.stockCheck({master: item.name}, 'size', $scope);
        });

        // handle found products ...
        $_element.on('on-products-found', function(event, data) {
            var $target_object,
                label,
                $scope = data.scope,
                $form_object = $scope.parents('form'),
                $sizeSelect_object = $($_identifiers.sizeSelect, $form_object),
                $colorSelect_object = $($_identifiers.colorSelect, $form_object);

            switch (data.target) {
                case 'size':
                    $target_object = $sizeSelect_object;
                    label   = Translator.trans('wishlist.select.size');
                    break;
                case 'color':
                    $target_object = $colorSelect_object;
                    label   = Translator.trans('wishlist.select.color');
                    break;
            }

            $('option', $target_object).remove();

            $target_object.append('<option value="">'+label+'</option>');
            $target_object.prop("disabled", false);

            $.each(data.data.products, function(key, value) {

                // we need this to filter out dubbs
                if ($('option[value="'+value[data.target]+'"]').length) {
                    return;
                }

                var label = value[data.target];

                if ('size' === data.target) {
                    label = value.size_label || value[data.target];
                }

                $target_object.append('<option value="'+value[data.target]+'" data-master="'+value.master+'" data-product-id="'+value.product_id+'">'+label+'</option>');
            });

            $target_object.focus();
        });

        // handle not-found cases
        $_element.on('on-no-products-found', function(event, data) {
            console.log(data);
        });

        // Size select
        // look up colors from a master and a size
        $($_identifiers.sizeSelect).on('change', function() {
            var $scope = $(this),
                $form_object = $scope.parents('form'),
                $masterField_object = $($_identifiers.masterField, $form_object),
                $sizeSelect_object = $($_identifiers.sizeSelect, $form_object);

            publicMethods.stockCheck({
                master : $masterField_object.val(),
                size   : $sizeSelect_object.val()
            }, 'color', $scope);
        });

        // set field state and focus
        $($_identifiers.colorSelect).on('change', function() {
            var $scope = $(this),
                $form_object = $scope.parents('form'),
                $quantitySelect_object = $($_identifiers.quantitySelect, $form_object),
                $productIdField_object = $($_identifiers.productIdField, $form_object);

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
    publicMethods.stockCheck = function(data, target, scope) {
        var xhr = $.ajax({
            url      : base_url + "stock-check",
            dataType : 'json',
            type     : 'GET',
            data     : data,
            async    : false
        });

        xhr.done(function(response) {
            response.target = target;
            response.scope = scope;
            if (response.status) {
                return $_element.trigger('on-products-found', response);
            }

            $_element.trigger('on-no-products-found', response);
        });

        xhr.fail(function() {
            dialoug.error(Translator.trans('notice'), Translator.trans('an.error.occurred'));
        });
    };

    return publicMethods;
});
