var quickorder = (function($) {
    var pub = {};

    pub.init = function() {
        _resetForm();

        var $_quickOrderForm     = $('.quickorder');
        var $_searchField        = $('.master', $_quickOrderForm);
        var $_itemContainerTable = $('.product-table');

        yatzy.compile('quickOrderItemTpl');

        $_searchField.typeahead({
            name       : "sku",
            remote     : {
                url: base_url + "quickorder/get-sku?name=%QUERY",
                beforeSend : function(jqXHR, settings) {
                    var query = settings.url.split('?')[1];
                    var params = {};
                    $.each(query.split('&'), function(index, element) {
                        var x = element.split('=');
                        params[x[0]] = x[1];
                    });

                    if ((typeof params.name === "undefined") ||
                        (params.name.length < 2) ||
                        (params.name.indexOf(' ') !== -1)
                    ) {
                        return false;
                    }
                }
            }
        });

        $_searchField.on('typeahead:autocompleted typeahead:selected', function(event, item) {
            var $context = $(this).closest('.quickorder');

            var XHR = $.ajax({
                url      : base_url + "stock-check",
                dataType : 'json',
                type     : 'GET',
                data     : { master : item.name },
                async    : false
            });

            XHR.done(function(response) {
                if (false === response.status) {
                    if (response.message) {
                        dialoug.alert(Translator.trans('notice', response.message));
                    }
                } else {
                    var $size_select = $('select.size', $context);
                    $size_select.find('option').remove();

                    if((typeof response.data.products !== "undefined") &&
                       (response.data.products.length > 0)
                    ){
                        $size_select.append($("<option></option>").
                            attr("value", '').
                            text(Translator.trans('quickorder.choose.size'))
                        );

                        var last = '';
                        $.each(response.data.products, function(key, value) {
                            if (value.size != last) {
                                $size_select.append($("<option></option>").
                                    attr("value",value.size).
                                    attr('data-master', value.master).
                                    text(value.size_label)
                                );
                                last = value.size;
                            }
                        });
                        $size_select.parent().show();
                        $size_select.focus();
                        $('.reset').show();
                    } else {
                        if (response.message) {
                            dialoug.alert(Translator.trans('notice'), response.message);
                        }
                    }
                }
            });

            XHR.fail(function() {
                dialoug.error(Translator.trans('notice'), Translator.trans('an.error.occurred'));
            });
        });

        // Events:
        // mouseup : desktop with mouse
        // keydown : desktop with keyboard
        // blur    : tablet/mobile
        $('.size', $_quickOrderForm).on('keydown mouseup blur change' ,function(e) {
            var $select        = $(this),
                selected_value = $select.val(),
                $context       = $(this).parent().parent(),
                is_ie          = $('html').hasClass('ie9') || $('html').hasClass('oldie'),
                is_rma         = $context.hasClass('rma-productreplacement')
            ;

            if ((!is_ie || !is_rma) && (e.type === 'change')) {
                // Event 'change' has to be dealt with in <=IE9. Change event
                // resolves that when changing with mouse, ie does not trigger any
                // other. This block returns only when we need to ignore the change
                // event. We are ignoring it when not IE and when not on RMA page.
                // Ignore it if its on the quickorder page.
                return;
            }

            // Compare the selected value to what it eventually already was. If the
            // same, ignore the event.
            // Fixes some IE9 issues where mouseup and blur was triggered to early.
            if ($select.data('selected-value') != selected_value) {
                if (e.type == 'keydown') {
                    var keyCode = e.keyCode || e.which;
                    if ((keyCode === 9) || (keyCode === 13)) {
                        e.preventDefault();
                        getColor($context);
                    }
                } else {
                    getColor($context);
                }
                $select.data('selected-value', $(this).val());
            }

            e.stopPropagation();
        });

        // Events:
        // mouseup : desktop with mouse
        // keydown : desktop with keyboard
        // blur    : tablet/mobile
        $('.color', $_quickOrderForm).on('keydown mouseup blur change' ,function(e){
            var $context = $(this).parent().parent(),
                is_ie    = $('html').hasClass('ie9') || $('html').hasClass('oldie'),
                is_rma   = $context.hasClass('rma-productreplacement')
            ;

            if ((!is_ie || !is_rma) && (e.type === 'change')) {
                // Event 'change' has to be dealt with in <=IE9. Change event
                // resolves that when changeing with mouse, ie doesnt trigger any
                // other. This block returns only when we need to ignore the change
                // event. We are ignoring it when not IE and when not on RMA page.
                // Ignore it if its on the quickorder page.
                return;
            }

            var $quantity_select = $context.find('.quantity');
            if (e.type == 'keydown') {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 9 || keyCode === 13) {

                    if ($(this).val() !== ''){
                        if ($quantity_select.length) {
                            e.preventDefault();
                            $quantity_select.parent().show();
                            $quantity_select.focus().select();
                        }
                        $('input[type=submit]', $context).show();
                    } else {
                        // Dont go to next input if color is empty.
                        e.preventDefault();
                    }
                }
            } else {
                if ($(this).val() !== '') {
                    $quantity_select.parent().show();
                    $quantity_select.focus().select();
                    $('input[type=submit]', $context).show();
                }
            }

            e.stopPropagation();
        });

        $_quickOrderForm.on('submit', function(event) {
            event.preventDefault();

            var $form    = $(this),
                title    = $('.master', $form).val(),
                master   = $('.size option:selected', $form).attr('data-master'),
                size     = $('.size option:selected', $form).val(),
                color    = $('.color option:selected', $form).val(),
                quantity = $('.quantity', $form).val()
            ;

            if ((master !== '') &&
                (size !== '') &&
                (color !== '') &&
                (quantity !== '')
            ) {
                var XHR = $.ajax({
                    url      : $form.attr('action'),
                    async    : false,
                    dataType : 'json',
                    type     : 'POST',
                    data     : {
                        master   : master,
                        size     : size,
                        color    : color,
                        quantity : quantity
                    }
                });

                XHR.done(function(response) {
                    if (false === response.status) {
                        if (response.message) {
                            dialoug.alert(Translator.trans('notice'), response.message);
                        }
                    } else {
                        var c = color.toString();
                        c = c.replace('/', '9');
                        var img = master.toString().replace(/[^a-zA-Z0-9_]/g, "-") + '_' + c.replace(/[^a-zA-Z0-9_]/g, "-");
                        img = cdn_url + 'images/products/thumb/57x100,' + img + '_overview_01.jpg';

                        window.scrollTo(window.scrollMinX, window.scrollMinY);
                        $.cookie('basket', response.data);
                        $('#mini-basket a.total').html(response.data);

                        var $mega_basket = $('#mega-basket'),
                            $mega_basket_table = $('.basket-table-body .content', $mega_basket);

                        if ($mega_basket.length) {
                            // Add the new product to the basket table.
                            $mega_basket_table.append('<div class="item new"><img src="' + img + '" />' + title + '<span class="right total">' + response.latest.price + '</span></div>');
                            // Update total price.
                            var item_count_regex = /\([0-9+]\) /;
                            var total = response.data.replace(item_count_regex, '');
                            $('.grand-total', $mega_basket).text(total);
                            $('.item-count', $mega_basket).text(response.data.match(item_count_regex));

                            $('body').trigger('basket_product_added');
                        }

                        $('tbody', $_itemContainerTable).prepend(yatzy.render('quickOrderItemTpl', {
                            img: img,
                            master: master,
                            title: title,
                            size: size,
                            color: color,
                            quantity: quantity,
                            latest: response.latest
                        }));

                        $('tfoot td.total', $_itemContainerTable).html(response.data);

                        var $proceedToBasket = $('.buttons a.proceed-to-basket');
                        if ($proceedToBasket.length > 0) {
                            $proceedToBasket.show();
                        } else {
                            $('.buttons').append('<a class="button right proceed-to-basket" href="'+base_url+'basket">'+Translator.trans('proceed')+'</a>');
                        }
                    }
                    _resetForm($form);
                });

                XHR.fail(function() {
                    dialoug.error(Translator.trans('notice!'), Translator.trans('an.error.occurred'));
                });
            }
        });

        $('.reset').click(function(e){
            e.preventDefault();
            var $context = $(this).closest('.quickorder');
            _resetForm($context);
        });

    };

    var getColor = function($context) {
        var $size = $context.find('.size option:selected');

        if ($size.val() !== '') {
            var XHR =  $.ajax({
                url      : base_url + "stock-check",
                async    : false,
                dataType : 'json',
                type     : 'GET',
                data     : {
                    master : $size.attr('data-master'),
                    size   : $size.val()
                }
            });

            XHR.done(function(response) {
                if (false === response.status) {
                    if (response.message) {
                        dialoug.alert(Translator.trans('notice', response.message));
                    }
                } else {

                    var $select = $('.color', $context);
                    $select.find('option').remove();

                    if ((typeof response.data.products !== "undefined") &&
                        (response.data.products.length > 0)
                    ){
                        $select.append($("<option></option>").
                            attr("value", '').
                            text(Translator.trans('quickorder.choose.color'))
                        );

                        var last = '';
                        $.each(response.data.products, function(key, value) {
                            if (last != value.color) {
                                $select.append($("<option></option>").
                                    attr("value",value.color).
                                    attr('data-master', value.master).
                                    text(value.color)
                                );
                                last = value.color;
                            }
                        });

                        $select.parent().show();
                        $select.focus().select();
                    } else {
                        if (response.message) {
                            dialoug.alert(Translator.trans('notice'), response.message);
                        }
                    }
                }
            });

            XHR.fail(function() {
                dialoug.error(Translator.trans('notice'), Translator.trans('an.error.occurred'));
            });
        }
    };

    var _resetForm = function($context) {
        $('.master', $context).typeahead('setQuery', '').focus();
        $('.size', $context).data('selected-value', '').find('option').remove();
        $('.color', $context).find('option').remove();
        $('.submit', $context).hide();
        $('.reset', $context).hide();
        $('.quantity', $context).val('1');
        $('label.off', $context).hide();
    };

    return pub;
})(jQuery);

$(document).ready(function(){
    if ($(".quickorder").length) {
        quickorder.init();
    }
});
