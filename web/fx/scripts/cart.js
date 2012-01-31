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

        var $act = $('<div id="cart-edit-element"><a href="" class="cancel">' + i18n.t('Cancel') + '</a></div>');
        var tr_offset = $tr.offset();
        var form_offset = $form.offset();

        $act.css({
          'top': tr_offset.top - $tr.height() - 8,
          'left' : 15,
          'height' : $tr.height() + 30,
          'width' : $tr.width(),
          'background-color' : '#615e5f'
        });
        $form.prepend($act);

        var $clone = $tr.clone();
        $act.prepend($clone);
        $('tr', $act).wrap('<table class="edit-element"><tbody></tbody></table>');

        var $edit = $('tr', $act);
        $('td:last', $edit).html('');

        $('a', $act).on('click', function(event) {
          event.preventDefault();
          $(this).off('click');
          $act.remove();

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

        $('select', $edit).on('change', function() {
          if (this.name == 'size') {
            $('select#color', $edit).replaceWith('<span>'+data['color']+'</span>');
            $('label[for="quantity"]', $edit).remove();
          }

          var request_data = {
            master : data.master,
            size   : $('select#size', $edit).val(),
            color  : $('select#color', $edit).val(),
          }

          $.ajax({
            url : base_url + 'rest/v1/stock-check',
            data : { master : data.master, size: this.value },
            dataType : 'json',
            async : false,
            success : function(responce, textStatus, jqXHR) {
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
         });
       });

      });
    };

    return pub;
  })($);

  cart.init();
})(jQuery);
