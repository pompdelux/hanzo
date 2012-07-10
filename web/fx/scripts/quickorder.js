var quickorder = (function($) {
  var pub = {};

  pub.init = function() {
    _resetForm();
    $( "#master" ).autocomplete({
        source: function( request, response ) {
          $.getJSON(
            base_url + "quickorder/get-sku",
            {name : request.term}, 
            function(data) {
              var items = [];

              $.each(data.data, function(key, val) {
                items.push(val);
              });
              response(items);
            }
          );
        },
        minLength: 3,
        appendTo: '#autocomplete-container',
        select: function( event, ui ) {
            event.preventDefault();
            $("#master").val(ui.item.label);
            console.log( ui.item ?
                "Selected: " + ui.item.label :
                "Nothing selected, input was " + this.value);

            $.ajax({
                url: base_url + "rest/v1/stock-check",
                dataType: 'json',
                type: 'GET',
                data: {master : ui.item.label},
                async: false,
                success: function(response, textStatus, jqXHR) {
                    if (false === response.status) {
                        if (response.message) {
                            dialoug.alert(ExposeTranslation.get('js:notice', response.message));
                        }
                    } else {

                        $('#size')
                            .find('option')
                            .remove()
                        ;
                        if(typeof response.data.products !== "undefined" && response.data.products.length > 0){

                            $('#size')
                                .append($("<option></option>")
                                .attr("value",'')
                                .text(ExposeTranslation.get('js:quickorder.choose.size')));

                            $.each(response.data.products, function(key, value) {
                                $('#size')
                                    .append($("<option></option>")
                                    .attr("value",value.size)
                                    .text(value.size));
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
    
    $('#size').live('keydown click' ,function(e){
        if (e.type == 'keydown') {
            var keyCode = e.keyCode || e.which; 
            if (keyCode == 9) {
                e.preventDefault();
                getColor();
            }
        }else{
            getColor();
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

                            $.each(response.data.products, function(key, value) {
                                $('#color')
                                    .append($("<option></option>")
                                    .attr("value",value.color)
                                    .text(value.color));
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
  
    $('#color').live('keydown click' ,function(e){
        if (e.type == 'keydown') {
            var keyCode = e.keyCode || e.which; 
            if (keyCode == 9) {
                e.preventDefault();

                if($(this).val() !== ''){
                    $('#quantity-label').show().find('#quantity').focus();
                    $('#submit').show();
                }
            }
        }else{
            if($(this).val() !== ''){
                $('#quantity-label').show().find('#quantity').focus();
                $('#submit').show();
            }
        }
    });

    $('#quickorder form').on('submit', function(event) {
        event.preventDefault();

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
                    $('table tbody').append('<tr><td>'+$('#master').val()+' '+$('#color').val()+' '+$('#size').val()+'</td><td>'+$('#quantity').val()+'</td></tr>');
                }
                _resetForm();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                dialoug.error(ExposeTranslation.get('js:notice!'), ExposeTranslation.get('js:an.error.occurred'));
            }
        });
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