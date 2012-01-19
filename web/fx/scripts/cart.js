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
        var $a = $(this);
        var $tr = $a.closest('tr');
        var $info = $tr.find('.info');
        var $form = $('#main form');

        var id = this.href;
        var data = {
          master : $info.find('a:first').text(),
          size : $info.find('label.size span').text(),
          color : $info.find('label.color span').text(),
        }

        var $act = $('<div id="cart-edit-element"><a href="">' + i18n.t('Cancel') + '</a></div>');
        var tr_offset = $tr.offset();
        var form_offset = $form.offset();

        $act.css({
          'top': tr_offset.top - $tr.height() - 3,
          'left' : 15,
          'height' : $tr.height(),
          'width' : $tr.width()
        });
        $form.prepend($act);

        $act.find('a').on('click', function(event) {
          event.preventDefault();
          $(this).off('click');
          $act.remove();

          $info.find('select').each(function(index, element) {
            $(this).replaceWith('<span>'+data[element.name]+'</span>')
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
              $info.find('label.size span').replaceWith($size);
            }
          }
        });

      });
    };

    return pub;
  })($);

  cart.init();
})(jQuery);
