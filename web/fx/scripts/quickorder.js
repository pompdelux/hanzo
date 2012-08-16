var quickorder = (function($) {
  var pub = {};

  pub.init = function() {
    _resetForm();

    $('#master').typeahead({
        source: function (typeahead, query) {
            if (query.length < 2) { return; }
            $.getJSON(
                base_url + "quickorder/get-sku",
                {name : query},
                function(data) {
                    return typeahead.process(data.data);
                }
            );
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
                            dialoug.alert(ExposeTranslation.get('js:notice', response.message));
                        }
                    } else {

                        $('#size').find('option').remove();

                        if(typeof response.data.products !== "undefined" && response.data.products.length > 0){

                            $('#size')
                                .append($("<option></option>")
                                .attr("value",'')
                                .text(ExposeTranslation.get('js:quickorder.choose.size')));

                            var last = '';
                            $.each(response.data.products, function(key, value) {
                                if (value.size != last {
                                    $('#size').append($("<option></option>").attr("value",value.size).text(value.size));
                                    last = value.size;
                                }
                            });
                            $('#size-label').show().find('#size').focus();
                            $('#reset').show();
                        } else {
                            if (response.message) {
                                dialoug.alert(ExposeTranslation.get('js:notice'), response.message);
                            }
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    dialoug.error(ExposeTranslation.get('js:notice'), ExposeTranslation.get('js:an.error.occurred'));
                }
            });
        }
    });

    // Events:
    // mouseup : desktop with mouse
    // keydown : desktop with keyboard
    // blur    : tablet/mobile
    //$('#size').on('keydown mouseup touchend' ,function(e){
    $('#size').on('keydown mouseup blur' ,function(e) {
        if (e.type == 'keydown') {
            var keyCode = e.keyCode || e.which;
            if (keyCode == 9) {
                e.preventDefault();
                getColor();
            }
        }else{
            getColor();
        }
        e.stopPropagation();
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
                          dialoug.alert(ExposeTranslation.get('js:notice', response.message));
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
                                .text(ExposeTranslation.get('js:quickorder.choose.color')));

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
                                dialoug.alert(ExposeTranslation.get('js:notice'), response.message);
                            }
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    dialoug.error(ExposeTranslation.get('js:notice'), ExposeTranslation.get('js:an.error.occurred'));
                }
            });
        }
    }

    // Events:
    // mouseup : desktop with mouse
    // keydown : desktop with keyboard
    // blur    : tablet/mobile
    //$('#color').on('keydown mouseup touchend' ,function(e){
    $('#color').on('keydown mouseup blur' ,function(e){
        if (e.type == 'keydown') {
            var keyCode = e.keyCode || e.which;
            if (keyCode == 9) {
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

        if((master != '') && (size != '') && (color != '') && (quantity != '')) {
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
                            dialoug.alert(ExposeTranslation.get('js:notice'), response.message);
                        }
                    }
                    else {
                        window.scrollTo(window.scrollMinX, window.scrollMinY);
                        $('#mini-basket a').html(response.data);
                        dialoug.slideNotice(response.message);
                        var img = master+'_basket_'+color;
                        img = cdn_url + 'images/products/thumb/60x60,' + img.toString().replace(/[^a-zA-Z0-9_]/g, "") + '.jpg';

                        $('table tbody').append('<tr><td><img src="'+img+'" alt="'+master+'"></td><td>'+master+' '+color+' '+size+'</td><td>'+quantity+'</td></tr>');
                    }
                    _resetForm();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    dialoug.error(ExposeTranslation.get('js:notice!'), ExposeTranslation.get('js:an.error.occurred'));
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
            .remove()
        $('#size-label').hide();
        ;
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
