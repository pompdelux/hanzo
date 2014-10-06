App.register('ProductFinder', function() {
    "use strict";

    var publicMethods = {};

    // private jquery elements.
    var $_form;
    var $_element;
    var $_searchField;
    var $_masterField;
    var $_sizeSelect;
    var $_colorSelect;
    var $_quantitySelect;

    /**
     * Initirat
     * @param $element
     */
    publicMethods.init = function($element) {
        $_element = $element;
        $_form    = $('form', $element);

        $_searchField    = $('input[name="q"]', $_form);
        $_masterField    = $('input[name="master"]', $_form);
        $_sizeSelect     = $('select[name="size"]', $_form);
        $_colorSelect    = $('select[name="color"]', $_form);
        $_quantitySelect = $('select[name="quantity"]', $_form);

        setupSearch();
        setupListeners();
    };

    var setupSearch = function() {
        // setup typeahead search
        $_searchField.typeahead({
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

    var setupListeners = function() {
        // handle typeahead requests
        $_searchField.on('typeahead:autocompleted typeahead:selected', function(event, item) {
            $_masterField.val(item.name);
            stockCheck({master: item.name}, 'size');
        });

        // handle found products ...
        $_element.on('on-products-found', function(event, data) {
            var $target;
            var label;

            switch (data.target) {
                case 'size':
                    $target = $_sizeSelect;
                    label   = Translator.trans('choice.choose_size');
                    break;
                case 'color':
                    $target = $_colorSelect;
                    label   = Translator.trans('choice.choose_color');
                    break;
            }

            $('option', $target).remove();

            $target.append('<option value="">'+label+'</option>');
            $target.prop("disabled", false);

            $.each(data.data.products, function(key, value) {
                // we need this to filter out dubbs
                if ($('option[value="'+value[data.target]+'"]').length) {
                    return;
                }

                var label = value[data.target];

                if ('size' === data.target) {
                    label = value.size_label || value[data.target];
                }

                $target.append('<option value="'+value[data.target]+'" data-master="'+value.master+'">'+label+'</option>');
            });

            $target.focus();
        });

        // handle not-found cases
        $_element.on('on-no-products-found', function(event, data) {
            console.log(data);
        });

        // look up colors from a master and a size
        $_sizeSelect.on('change', function() {
            stockCheck({
                master : $_masterField.val(),
                size   : $_sizeSelect.val()
            }, 'color');
        });

        // set field state and focus
        $_colorSelect.on('change', function() {
            $_quantitySelect.prop('disabled', false);
            $_quantitySelect.focus();
        });

        $_form.on('submit', function(event) {
            event.preventDefault();

            console.log($_form.serialize());
        });
    };

    var stockCheck = function(data, target) {
        var xhr = $.ajax({
            url      : base_url + "stock-check",
            dataType : 'json',
            type     : 'GET',
            data     : data,
            async    : false
        });

        xhr.done(function(response) {
            response.target = target;
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
