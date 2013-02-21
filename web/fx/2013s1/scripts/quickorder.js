var quickorder = (function($) {
  var pub = {};

  pub.init = function() {
    _resetForm();

    $('#master').typeahead({
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
            $("#master").val(object);

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

                        $('#size').find('option').remove();

                        if(typeof response.data.products !== "undefined" && response.data.products.length > 0){

                            $('#size')
                                .append($("<option></option>")
                                .attr("value",'')
                                .text(Translator.get('js:quickorder.choose.size')));

                            var last = '';
                            $.each(response.data.products, function(key, value) {
                                if (value.size != last) {
                                    $('#size').append($("<option></option>").attr("value",value.size).text(value.size));
                                    last = value.size;
                                }
                            });
                            $('#size-label').show().find('#size').focus();
                            $('#reset').show();
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

    function getColor() {
        if($('#size').val() !== ''){

            $.ajax({
                url: base_url + "rest/v1/stock-check",
                dataType: 'json',
                type: 'GET',
                data: {
                    master : $('#master').val(),
                    size : $('#size').val()
                },
                async: false,
                success: function(response, textStatus, jqXHR) {
                    if (false === response.status) {
                        if (response.message) {
                          dialoug.alert(Translator.get('js:notice', response.message));
                        }
                    } else {

                        $('#color')
                            .find('option')
                            .remove()
                        ;
                        if(typeof response.data.products !== "undefined" && response.data.products.length > 0){

                            $('#color')
                                .append($("<option></option>")
                                .attr("value",'')
                                .text(Translator.get('js:quickorder.choose.color')));

                            var last = '';
                            $.each(response.data.products, function(key, value) {
                                if (last != value.color) {
                                    $('#color').append($("<option></option>").attr("value",value.color).text(value.color));
                                    last = value.color;
                                }
                            });
                            $('#color-label').show().find('#color').focus();
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
    //$('#size').on('keydown mouseup touchend' ,function(e){
    $('#size').on('keydown mouseup blur' ,function(e) {
        if (e.type == 'keydown') {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 9 || keyCode === 13) {
                e.preventDefault();
                getColor();
            }
        }else{
            getColor();
        }
        e.stopPropagation();
    });

    // Events:
    // mouseup : desktop with mouse
    // keydown : desktop with keyboard
    // blur    : tablet/mobile
    //$('#color').on('keydown mouseup touchend' ,function(e){
    $('#color').on('keydown mouseup blur' ,function(e){
        if (e.type == 'keydown') {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 9 || keyCode === 13) {
                e.preventDefault();

                if($(this).val() !== ''){
                    $('#quantity-label').show().find('#quantity').focus().select();
                    $('#submit').show();
                }
            }
        }else{
            if($(this).val() !== ''){
                $('#quantity-label').show().find('#quantity').focus().select();
                $('#submit').show();
            }
        }
        e.stopPropagation();
    });

    $('#quickorder form').on('submit', function(event) {
        event.preventDefault();

        var master = $('#master').val(),
            size = $('#size').val(),
            color = $('#color').val(),
            quantity = $('#quantity').val()
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

                        var img = master.toString().replace(/[^a-zA-Z0-9_]/g, "-") + '_' + color.toString().replace(/[^a-zA-Z0-9_]/g, "-");
                        img = cdn_url + 'images/products/thumb/38x75,' + img + '_set_01.jpg';
                        $('table tbody').prepend(' ' +
                            '<tr> ' +
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
                                '<a href="'+base_url+'remove-from-basket/'+response.latest.id+'" class="delete"><img src="'+cdn_url+'fx/images/delete_icon.png" alt="'+Translator.get('js:delete')+'"></a> '+
                                '<a href="'+response.latest.id+'" class="edit"><img src="'+cdn_url+'fx/images/edit_icon.png" alt="'+Translator.get('js:edit')+'"></a> '+
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

    $('#reset').click(function(e){
        e.preventDefault();
        _resetForm();
    });

  };

    pub.getColor = function() {

    };

    _resetForm = function() {
        $('#master').val('').focus();
        $('#size')
            .find('option')
            .remove();
        $('#size-label').hide();

        $('#color')
            .find('option')
            .remove()
        ;
        $('#color-label').hide();

        $('#submit').hide();
        $('#reset').hide();
        $('#quantity').val('1');
        $('#quantity-label').hide();
    };

  return pub;
})(jQuery);

$(document).ready(function(){
    if ($("#quickorder").length) {
      quickorder.init();
    }
});
