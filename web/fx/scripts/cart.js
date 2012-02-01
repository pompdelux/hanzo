(function($) {

  var cart = (function($) {
    var pub = {}

    pub.init = function() {
      $('a.delete').on('click', function(event) {
        event.preventDefault();
        var $a = $(this);
        var name = $a.closest('tr').find('.info a').text();

        // warn the user before removing the product.
        dialoug.confirm('Notice', i18n.t('delete from basket warning', { '%product%' : name }), function(choice) {
          if (choice == 'ok') {
            $.ajax({
              url : $a.attr('href'),
              dataType: 'json',
              async : false,
              success : function(responce, textStatus, jqXHR) {
                if (responce.status) {
                  // add effects to the removal of a basket row
                  $a.closest('tr').fadeOut(function() {
                    $(this).remove();
                  });

                  // update elements
                  $('#mini-basket a').html('(' + responce.data.quantity + ') ' + responce.data.total);
                  $('tfoot td.total').text(responce.data.total);

                  // remove the proceed button if there are no products in the cart
                  if (0 == responce.data.quantity) {
                    $('.buttons a.button.proceed').remove();
                  }
                }
              }
            });
          }
        });
      });

      $('a.edit').on('click', function(event) {

        $('#cboxOverlay').css({"opacity": 0.9, "cursor": "auto"}).show();

        event.preventDefault();

        var $a    = $(this);
        var $tr   = $a.closest('tr');
        var $info = $('.info', $tr);
        var $form = $('#main form');

        var id = this.href;
        var data = {
          master : $('a:first', $info).text(),
          size   : $('label.size span', $info).text(),
          color  : $('label.color span', $info).text(),
        }

        var $act = $('<div id="cart-edit-element"><a href="" class="button">' + i18n.t('Cancel') + '</a></div>');
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
          success : function(responce, textStatus, jqXHR) {
            if (responce.status) {
              var $size = $('<select id="size" name="size"><option value="">' + i18n.t('Choose') + '</option></select>');

              var used = [];
              $.each(responce.data.products, function(index, product) {
                if (-1 == $.inArray(product.size, used)) {
                  used.push(product.size);
                  $size.append('<option value="'+product.size+'">'+product.size+'</option>');
                }
              });
              $('label.size span', $edit).replaceWith($size);
            }
          }
        });

        $edit.on('change', 'select#size, select#color', function() {
          var name = this.name;
          if (name == 'size') {
            $('select#color', $edit).replaceWith('<span>'+data['color']+'</span>');
          }
          $('label[for="quantity"]', $edit).remove();

          var request_data = {
            master : data.master,
            size   : $('select#size', $edit).val(),
            color  : $('select#color', $edit).val(),
          }

          $.ajax({
            url : base_url + 'rest/v1/stock-check',
            data : request_data,
            dataType : 'json',
            async : false,
            success : function(responce, textStatus, jqXHR) {
              if (name == 'size') {
                if (responce.status) {
                  var $color = $('<select id="color" name="color"><option value="">' + i18n.t('Choose') + '</option></select>');
                  var used = [];
                  $.each(responce.data.products, function(index, product) {
                    if (-1 == $.inArray(product.color, used)) {
                      used.push(product.color);
                      $color.append('<option value="'+product.color+'">'+product.color+'</option>');
                    }
                  });
                  $('label.color span', $edit).replaceWith($color);
                }
              }
              else {
                if (responce.status) {
                  var product = responce.data.products[0];
console.log(product);
                  if (product.date) {
                    dialoug.confirm(i18n.t('Notice!'), responce.message, function(c) {
                      if (c != 'ok') {
                        return;
                      }
                    });
                  }

                  $('.info', $edit).append('<label for="quantity">' + i18n.t('Quantity') + ': <select name="quantity" id="quantity"><option value="">' + i18n.t('Choose') + '</option></select></label>');
                  for (i=1; i<11; i++) {
                    $('.info select#quantity', $edit).append('<option value="'+i+'">'+i+'</option>');
                  }
                }
              }
            }
          });
        });

        $($edit).on('change', 'select#quantity', function() {
          $(this).closest('label').after('<input type="button" class="button" value="'+i18n.t('Update')+'">');
        });
      });
    };

    return pub;
  })($);

  cart.init();
})(jQuery);
