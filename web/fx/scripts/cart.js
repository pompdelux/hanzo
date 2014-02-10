(function($) {

  var cart = (function($) {
    var pub = {};
    var is_mobile = $('body').hasClass('is-mobile') ? true : false;

    pub.init = function() {
      var $basket = $('.basket');
      if ($basket.length === 0) {
        return;
      }

      $basket.on('click', 'a.delete', function(event) {
        event.preventDefault();
        var $a = $(this);
        var $item = $a.closest('.item');
        var name = $('.info a', $item).text();

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
                  $item.fadeOut(function() {
                    $(this).remove();
                  });

                  // update elements
                  var content = '(' + response.data.quantity + ') ' + response.data.total;
                  $.cookie('basket', content);
                  $('#mini-basket a').html(content);
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

      $basket.on('click', 'a.edit', function(event) {
        var $target;
        if (is_mobile) {
          $target = $(this).closest('.item');
        } else {
          $('#cboxOverlay').css({"opacity": 0.9, "cursor": "auto", "z-index": 9998}).show();
        }

        event.preventDefault();

        var $a    = $(this);
        var $item = $a.closest('.item');
        var $info = $('.info', $item);
        $item.addClass('current');
        var $form = $('#main form');

        var id = this.href;
        var data = {
          master : $info.data('master'),
          size   : $('div.size span', $info).text(),
          color  : $('div.color span', $info).text()
        };

        var $html = $('<div id="cart-edit-element"><a href="" class="button text-button">' + Translator.get('js:cancel') + '</a></div>');

        if (is_mobile) {
          $target.before($html);
        } else {
          var tr_offset = $item.offset();
          var form_offset = $form.offset();
          $html.css({
            'top': tr_offset.top - 18,
            'left' : tr_offset.left,
            'width' : $item.width() - 10
          });
          $('body').prepend($html);
        }

        var $clone = $item.clone();
        $clone.removeClass('current');
        $html.prepend($clone);

        var $edit = $('.item', $html);
        if (!is_mobile) {
          $('.item', $html).wrap('<table class="edit-element"><tbody></tbody></table>');
          $('.actions', $edit).html('');
        } else {
          $('.buttons', $edit).hide();
        }


        $('a', $html).on('click', function(event) {
          event.preventDefault();
          $(this).off('click');
          $html.remove();
          if (is_mobile) {
            $('.current').removeClass('current');
          } else {
            $('#cboxOverlay').hide();
          }

          $('select', $info).each(function(index, element) {
            $(this).replaceWith('<span>'+data[element.name]+'</span>');
          });
        });

        $.ajax({
          url : base_url + 'stock-check',
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
                  $size.append('<option value="'+product.size+'">'+product.size_label+'</option>');
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
          $('input.button', $html).remove();

          var request_data = {
            master : data.master,
            size   : $('select#size', $edit).val(),
            color  : $('select#color', $edit).val()
          };

          $.ajax({
            url : base_url + 'stock-check',
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
          if ($('.button.button-right', $edit).length === 0) {
            $(this).closest('div').after('<input type="button" class="button button-right" value="'+Translator.get('js:update')+'">');
          }
        });

        $edit.on('click', 'input.button', function() {
          var $button = $(this);
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
                    // do stuff ?
                  }
                  else {
                    if (product.date) {
                      var notice = Translator.get('js:late.delivery', {'product' : request_data.master+' '+request_data.color+' '+request_data.size , 'date' : product.date});
                      $html.append('<div class="delivery-notice">'+notice+'</div>');
                      $info.data('confirmed', true);
                      return;
                    }
                  }
                }

                // update containers, and close overlay
                if (response.data.normal !== undefined) {
                  $('div.size span', $info).text(request_data.size);
                  $('div.color span', $info).text(request_data.color);
                  $('.quantity', $item).text(request_data.quantity);

                  $('.price', $item).text(response.data.sales || response.data.normal);
                  $('.total', $item).text(response.data.sales_total || response.data.normal_total);
                  $('.actions a.delete', $item).attr('href',base_url+'remove-from-basket/'+response.data.product_id);
                  $('.actions a.edit', $item).attr('href', response.data.product_id);

                  // totals
                  $.cookie('basket', response.data.basket);
                  $('#mini-basket a').text(response.data.basket);
                  var find = /\([0-9+]\) /;
                  var total = response.data.basket.replace(find, '');
                  $info.data('product_id', response.data.product_id);
                  $('.grand-total').text(total);
                }

                // close overlay
                $('a', $html).click();
              } else {
                dialoug.notice(response.message, 'error', null, $button);
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
