(function($) {

  var cart = (function($) {
    var pub = {};

    pub.init = function() {
      var $table = $('table.product-table');
      if ($table.length != 1) {
        return;
      }


      $table.on('click', 'a.delete', function(event) {
        event.preventDefault();
        var $a = $(this);
        var name = $a.closest('tr').find('.info a').text();

        // warn the user before removing the product.
        dialoug.confirm(Translator.get('js:notice'), Translator.get('js:delete.from.basket.warning', { 'product' : name }), function(choice) {
          if (choice == 'ok') {
            $.ajax({
              url : $a.attr('href'),
              dataType: 'json',
              async : false,
              success : function(response, textStatus, jqXHR) {
                if (response.status) {
                  // add effects to the removal of a basket row
                  $a.closest('tr').fadeOut(function() {
                    $(this).remove();
                  });

                  // update elements
                  $('#mini-basket a').html('(' + response.data.quantity + ') ' + response.data.total);
                  $('tfoot td.total').text(response.data.total);

                  // remove the proceed button if there are no products in the cart
                  if (0 === response.data.quantity) {
                    $('.buttons a.button.proceed').remove();
                    $('.buttons a.button.proceed-to-basket').remove(); // Used on quickorder
                  }
                }
              }
            });
          }
        });
      });

      $table.on('click', 'a.edit', function(event) {
        $('#cboxOverlay').css({"opacity": 0.9, "cursor": "auto", "z-index": 9998}).show();

        event.preventDefault();

        var $a    = $(this);
        var $tr   = $a.closest('tr');
        var $info = $('.info', $tr);
        var $form = $('#main form');

        var id = this.href;
        var data = {
          master : $('a:first', $info).text(),
          size   : $('div.size span', $info).text(),
          color  : $('div.color span', $info).text()
        };

        var $act = $('<div id="cart-edit-element"><a href="" class="button left">' + Translator.get('js:cancel') + '</a></div>');
        var tr_offset = $tr.offset();
        var form_offset = $form.offset();

        $act.css({
          'top': tr_offset.top - 18,
          'left' : tr_offset.left,
          'height' : $tr.height() + 30,
          'width' : $tr.width(),
          'background-color' : '#615e5f'
        });
        $('body').prepend($act);

        var $clone = $tr.clone();
        $act.prepend($clone);
        $('tr', $act).wrap('<table class="edit-element"><tbody></tbody></table>');

        var $edit = $('tr', $act);
        $('td:last', $edit).html('');

        $('a', $act).on('click', function(event) {
          event.preventDefault();
          $(this).off('click');
          $act.remove();
          $('#cboxOverlay').hide();

          $('select', $info).each(function(index, element) {
            $(this).replaceWith('<span>'+data[element.name]+'</span>');
          });
        });

        $.ajax({
          url : base_url + 'rest/v1/stock-check',
          data : { master : data.master },
          dataType : 'json',
          async : false,
          success : function(response, textStatus, jqXHR) {
            if (response.status) {
              var $size = $('<select id="size" name="size"><option value="">' + Translator.get('js:choose') + '</option></select>');

              var used = [];
              $.each(response.data.products, function(index, product) {
                if (-1 == $.inArray(product.size, used)) {
                  used.push(product.size);
                  $size.append('<option value="'+product.size+'">'+product.size+'</option>');
                }
              });
              $('div.size span', $edit).replaceWith($size);
            }
          }
        });

        $edit.on('change', 'select#size, select#color', function() {
          var name = this.name;
          var $this = $(this);

          dialoug.loading($this);

          if (name == 'size') {
            $('select#color', $edit).replaceWith('<span>'+data['color']+'</span>');
          }
          $('div.quantity', $edit).remove();
          $('input.button', $act).remove();

          var request_data = {
            master : data.master,
            size   : $('select#size', $edit).val(),
            color  : $('select#color', $edit).val()
          };

          $.ajax({
            url : base_url + 'rest/v1/stock-check',
            data : request_data,
            dataType : 'json',
            async : false,
            success : function(response, textStatus, jqXHR) {
              if (name == 'size') {
                if (response.status) {
                  var $color = $('<select id="color" name="color"><option value="">' + Translator.get('js:choose') + '</option></select>');
                  var used = [];
                  $.each(response.data.products, function(index, product) {
                    if (-1 == $.inArray(product.color, used)) {
                      used.push(product.color);
                      $color.append('<option value="'+product.color+'">'+product.color+'</option>');
                    }
                  });
                  $('div.color span', $edit).replaceWith($color);
                }
              }
              else {
                if (response.status) {
                  var product = response.data.products[0];
                  if (product.date) {
                    dialoug.confirm(Translator.get('js:notice'), response.message, function(c) {
                      if (c != 'ok') {
                        $('#cboxOverlay').css({"opacity": 0.9, "cursor": "auto", "z-index": 9998}).show();
                        return;
                      }
                    });
                  }

                  $('.info', $edit).append('<div class="quantity"><label for="quantity">' + Translator.get('js:quantity') + ':</label> <select name="quantity" id="quantity"><option value="">' + Translator.get('js:choose') + '</option></select></div>');
                  for (var i=1; i<11; i++) {
                    $('.info select#quantity', $edit).append('<option value="'+i+'">'+i+'</option>');
                  }
                }
              }
              dialoug.stopLoading();
            }
          });
        });

        $edit.on('change', 'select#quantity', function() {
          $(this).closest('div').after('<input type="button" class="button" value="'+Translator.get('js:update')+'">');
        });

        $edit.on('click', 'input.button', function() {
          var request_data = {
            'product_to_replace': $info.data('product_id'),
            'master': data.master
          };

          if ($info.data('confirmed')) {
            request_data.confirmed = true;
          }

          $('select', $edit).each(function(index, element) {
            request_data[element.name] = element.value;
          });

          $.ajax({
            url : base_url + 'replace-basket-item',
            data : request_data,
            dataType : 'json',
            async : false,
            type: 'POST',
            success : function(response, textStatus, jqXHR) {
              if (response.status) {
                if (response.data.products !== undefined) {
                  var product = response.data.products[0];
                  if (product === undefined) {
                    // no product - this is bad...
                  }
                  else {
                    if (product.date) {
                      var notice = Translator.get('js:late.delivery', {'product' : request_data.master+' '+request_data.color+' '+request_data.size , 'date' : product.date});
                      $act.append('<div class="delivery-notice">'+notice+'</div>');
                      $info.data('confirmed', true);
                      return;
                    }
                  }
                }

                // update containers, and close overlay
                if (response.data.normal !== undefined) {
                  $('div.size span', $info).text(request_data.size);
                  $('div.color span', $info).text(request_data.color);
                  $('td.quantity', $tr).text(request_data.quantity);

                  $('td.price', $tr).text(response.data.sales || response.data.normal);
                  $('td.total', $tr).text(response.data.sales_total || response.data.normal_total);
                  $('td.actions a.delete', $tr).attr('href',base_url+'remove-from-basket/'+response.data.product_id);
                  $('td.actions a.edit', $tr).attr('href', response.data.product_id);

                  // totals
                  $('#mini-basket a').text(response.data.basket);
                  var find = /\([0-9+]\) /;
                  var total = response.data.basket.replace(find, '');
                  $info.data('product_id', response.data.product_id);
                  $('tfoot .total').text(total);
                }

                // close overlay
                $('a', $act).click();
              }
            }
          });
        });
      });
    };

    return pub;
  })($);

  cart.init();
})(jQuery);
