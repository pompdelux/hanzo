var quickorder = (function($) {
  var pub = {};

  pub.init = function() {
    _resetForm();

    $('.quickorder .master').typeahead({
        source: function (typeahead, query) {
            if (query.length < 2) { return; }
            $.ajax({
                url: base_url + "quickorder/get-sku",
                dataType: 'json',
                type: 'GET',
                data: {name : query},
                async: false,
                cache: false,
                success: function(response, textStatus, jqXHR) {
                    return typeahead.process(response.data);
                }
            });
        },
        onselect: function( object ) {
            $(this).val(object);

            $context = this.$element.parent().parent();
            $size_select = $context.find('.size');

            $.ajax({
                url: base_url + "rest/v1/stock-check",
                dataType: 'json',
                type: 'GET',
                data: {master : object},
                async: false,
                success: function(response, textStatus, jqXHR) {
                    if (false === response.status) {
                        if (response.message) {
                            dialoug.alert(Translator.get('js:notice', response.message));
                        }
                    } else {

                        $size_select.find('option').remove();

                        if(typeof response.data.products !== "undefined" && response.data.products.length > 0){

                            $size_select
                                .append($("<option></option>")
                                .attr("value",'')
                                .text(Translator.get('js:quickorder.choose.size')));

                            var last = '';
                            $.each(response.data.products, function(key, value) {
                                if (value.size != last) {
                                    $size_select.append($("<option></option>").attr("value",value.size).text(value.size));
                                    last = value.size;
                                }
                            });
                            $size_select.parent().show();
                            $size_select.focus();
                            $context.find('.reset').show();
                        } else {
                            if (response.message) {
                                dialoug.alert(Translator.get('js:notice'), response.message);
                            }
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
                }
            });
        }
    });

    function getColor($context) {
        if($context.find('.size').val() !== ''){

            $.ajax({
                url: base_url + "rest/v1/stock-check",
                dataType: 'json',
                type: 'GET',
                data: {
                    master : $context.find('.master').val(),
                    size : $context.find('.size').val()
                },
                async: false,
                success: function(response, textStatus, jqXHR) {
                    if (false === response.status) {
                        if (response.message) {
                          dialoug.alert(Translator.get('js:notice', response.message));
                        }
                    } else {

                        $select = $('.color', $context);
                        $select.find('option').remove();

                        if(typeof response.data.products !== "undefined" && response.data.products.length > 0){

                            $select.append($("<option></option>")
                                .attr("value",'')
                                .text(Translator.get('js:quickorder.choose.color')));

                            var last = '';
                            $.each(response.data.products, function(key, value) {
                                if (last != value.color) {
                                    $select
                                        .append($("<option></option>")
                                        .attr("value",value.color)
                                        .text(value.color));
                                    last = value.color;
                                }
                            });

                            $select.parent().show();
                            $select.focus().select();
                        }else{
                            if (response.message) {
                                dialoug.alert(Translator.get('js:notice'), response.message);
                            }
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
                }
            });
        }
    }

    // Events:
    // mouseup : desktop with mouse
    // keydown : desktop with keyboard
    // blur    : tablet/mobile
    $('.quickorder .size').on('keydown mouseup blur change' ,function(e) {

        var selected_value = $(this).val(),
            $context = $(this).parent().parent(),
            is_ie = $('html').hasClass('ie9') || $('html').hasClass('oldie'),
            is_rma = $context.hasClass('rma-productreplacement');

        if((!is_ie || !is_rma) && e.type === 'change') {
            // Event 'change' has to be dealt with in <=IE9. Change event
            // resolves that when changeing with mouse, ie doesnt trigger any
            // other. This block returns only when we need to ignore the change
            // event. We are ignoring it when not IE and when not on RMA page.
            // Ignore it if its on the quickorder page.
            return;
        }

        // Compare the selected value to what it eventually already was. If the
        // same, ignore the event.
        // Fixes some IE9 issues where mouseup and blur was triggered to early.
        if($(this).data('selected-value') != selected_value) {
            if (e.type == 'keydown') {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 9 || keyCode === 13) {
                    e.preventDefault();
                    getColor($context);
                }
            }else{
                getColor($context);
            }
            $(this).data('selected-value', $(this).val());
        }
        e.stopPropagation();
    });

    // Events:
    // mouseup : desktop with mouse
    // keydown : desktop with keyboard
    // blur    : tablet/mobile
    $('.quickorder .color').on('keydown mouseup blur change' ,function(e){
        var $context = $(this).parent().parent(),
            is_ie = $('html').hasClass('ie9') || $('html').hasClass('oldie'),
            is_rma = $context.hasClass('rma-productreplacement');

        if((!is_ie || !is_rma) && e.type === 'change') {
            // Event 'change' has to be dealt with in <=IE9. Change event
            // resolves that when changeing with mouse, ie doesnt trigger any
            // other. This block returns only when we need to ignore the change
            // event. We are ignoring it when not IE and when not on RMA page.
            // Ignore it if its on the quickorder page.
            return;
        }

        $quantity_select = $context.find('.quantity');
        if (e.type == 'keydown') {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 9 || keyCode === 13) {
                e.preventDefault();

                if($(this).val() !== ''){
                    $quantity_select.parent().show();
                    $quantity_select.focus().select();
                    $('input[type=submit]', $context).show();
                }
            }
        }else{
            if($(this).val() !== ''){
                $quantity_select.parent().show();
                $quantity_select.focus().select();
                $('input[type=submit]', $context).show();
            }
        }
        e.stopPropagation();
    });

    $('form.quickorder').on('submit', function(event) {
        event.preventDefault();

        var master = $(this).find('.master').val(),
            size = $(this).find('.size').val(),
            color = $(this).find('.color').val(),
            quantity = $(this).find('.quantity').val()
        ;

        if((master !== '') && (size !== '') && (color !== '') && (quantity !== '')) {
            var $form = $(this);
            $.ajax({
                url: $form.attr('action'),
                dataType: 'json',
                type: 'POST',
                data: $form.serialize(),
                async: false,
                success: function(response, textStatus, jqXHR) {
                    if (false === response.status) {
                        if (response.message) {
                            dialoug.alert(Translator.get('js:notice'), response.message);
                        }
                    }
                    else {
                        window.scrollTo(window.scrollMinX, window.scrollMinY);
                        $.cookie('basket', response.data);
                        $('#mini-basket a').html(response.data);
                        dialoug.slideNotice(response.message);

                        var c = color.toString();
                        c = c.replace('/', '9');
                        var img = master.toString().replace(/[^a-zA-Z0-9_]/g, "-") + '_' + c.replace(/[^a-zA-Z0-9_]/g, "-");
                        img = cdn_url + 'images/products/thumb/57x100,' + img + '_overview_01.jpg';

                        $('table tbody').prepend(' ' +
                            '<tr class="item"> ' +
                              '<td class="image"><img src="'+img+'" alt="'+master+'"> '+
                                '<div class="info" data-product_id="'+response.latest.id+'" data-confirmed=""> '+
                                  '<a href="'+base_url+'product/view/'+response.latest.id+'">'+master+'</a> '+
                                  '<div class="size"> '+
                                    '<label>'+Translator.get('js:size')+':</label> '+
                                    '<span>'+size+'</span> '+
                                  '</div> '+
                                  '<div class="color"> '+
                                    '<label>'+Translator.get('js:color')+':</label> '+
                                    '<span>'+color+'</span> '+
                                  '</div> '+
                                '</div> '+
                              '</td> '+
                              '<td class="right date"> '+
                              ''+response.latest.expected_at+' '+
                              '</td> '+
                              '<td class="right price">'+response.latest.single_price+'</td> '+
                              '<td class="center quantity">'+quantity+'</td> '+
                              '<td class="actions"> '+
                                '<a href="'+base_url+'remove-from-basket/'+response.latest.id+'" class="sprite delete"></a> '+
                                '<a href="'+response.latest.id+'" class="sprite edit"></a> '+
                              '</td> '+
                              '<td class="right total">'+response.latest.price+'</td> '+
                            '</tr>');
                        $('table tfoot td.total').html(response.data);
                        if($('.buttons a.proceed-to-basket').length > 0){
                            $('.buttons a.proceed-to-basket').show();
                        }else{
                            $('.buttons').append('<a class="button right proceed-to-basket" href="'+base_url+'basket">'+Translator.get('js:proceed')+'</a>');
                        }
                    }
                    _resetForm();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    dialoug.error(Translator.get('js:notice!'), Translator.get('js:an.error.occurred'));
                }
            });
        }

    });

    $('.reset').click(function(e){
        e.preventDefault();
        $context = $(this).parent();
        _resetForm($context);
    });

  };

    _resetForm = function($context) {
        $('.master', $context).val('').focus();
        $('.size', $context)
            .data('selected-value', '')
            .find('option')
            .remove();

        $('.color', $context)
            .find('option')
            .remove()
        ;

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
