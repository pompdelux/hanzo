/* 
 * @copyright bellcom open source aps
 * @author ulrik nielsen <un@bellcom.dk>
 */

(function ($) {
  var methods = {
    init : function (options) {
      return this.each(function () {
        // design
        methods.basket_states();
        // triggers
        $(this).find('.pane .item a').bind('click.mannequin', methods.layer_click);
        $(this).find('#mannequin-basket td.actions a').live('click.mannequin', methods.basket_item_click);
        $(this).find('#mannequin-basket td.name a').live('click.mannequin', function(event) {
          event.preventDefault();
          this.blur();
          window.open(this.href);
        });
      });
    },
    
    destroy : function () {
      return this.each(function () {
        $(window).unbind('.mannequin');
      });
    },

    layer_click : function (event) {
      event.preventDefault();
      this.blur();

      var data = eval('mannequin_data_object.' + this.id);
      var existing_layer = $('#mannequin-doll .mannequin-layer-' + data.layer);
      var existing_layer_data = null;
      var swap_layer = null;

      // if a layer already exist we need to remove the old, before adding the new
      if (existing_layer.length) {
        existing_layer_data = eval('mannequin_data_object.' + existing_layer.attr('id'));
        $('#mannequin-doll .mannequin-layer-' + data.layer).remove();
        swap_layer = existing_layer.attr('id');
        if (existing_layer_data.image == data.image) {
          methods.remove_doll_layer(swap_layer);
        }
      }

      if (!existing_layer_data || existing_layer_data.image != data.image) {
        $('#mannequin-doll .layers').append('<img src="/images/mannequin/' + data.image + '" class="layer mannequin-layer-' + data.layer + '" id="' + this.id + '" style="z-index: ' + (data.layer * 10) + ';" />');
        data.action = 'add';
        data.key = this.id;
        $.post('/mannequin.php', data, function (result) {
          if (result.code == 200) {
            $('#mannequin-basket tbody tr.empty').remove();
            if (swap_layer) {
              $('#mannequin-basket tbody tr.' + swap_layer).replaceWith(result.html);
            } else {
              $('#mannequin-basket tbody').append(result.html);
            }
            methods.basket_states();
          }
        }, 'json');
      }
    },

    // basket methods

    basket_item_click : function (event) {
      event.preventDefault();
      this.blur();

      var key = $(this).parent().parent().attr('class').split(' ')[0];

      // remove item from "basket"
      if ($(this).hasClass('remove')) {
        methods.remove_doll_layer(key);
      }

      var data = eval('mannequin_data_object.' + key);

      // get color, size and quantity selectors so we can let the
      // user add this product to the "real" basket
      if ($(this).hasClass('edit')) {
        data.action = 'edit';
        data.step = 'size';
        $.post('/mannequin.php', data, function (result) {
          if (result.code == 200) {
            $('tr.' + key + ' td.size').html(result.html);
            $('tr.' + key + ' td.size select').change(function() {
              methods.item_size_selected(this, data);
            });
          }
        }, 'json');
      }

      // add selected product to the basket
      if ($(this).hasClass('add')) {
        var id = $('tr.' + key + ' td.size select option:selected').val().split(':')[1];
        var quantity = $('tr.' + key + ' td.quantity select option:selected').val();

        methods.add_to_basket(id, quantity, data);
      }
    },

    remove_doll_layer : function (key) {
      var data = eval('mannequin_data_object.' + key);
      data.key = key;
      data.action = $(this).attr('class ');
      $('.mannequin-layer-' + data.layer).remove();
      methods.basket_states();
    },

    basket_states : function () {
      if ($('#mannequin-basket tbody tr').length) {
        $('#mannequin-basket tfoot').show();

        var price = 0;
        $('#mannequin-basket tbody tr').each(function() {
          var data = eval('mannequin_data_object.' + $(this).attr('class').split(' ')[0]);
          price += data.raw_price;
        });
        $('#mannequin-basket tfoot td.total').text(i18n.mannequinPrice.replace(':price:', price));
      } else {
        $('#mannequin-basket tfoot').hide();
        $('#mannequin-basket tbody').append('<tr class="empty"><td colspan="6">' + i18n.mannequinEmpty + '</td></tr>');
      }
    },

    item_size_selected : function (elm, data) {
      data.size = $(elm).find('option:selected').val().split(':')[0];
      data.step = 'quantity';
      $.post('/mannequin.php', data, function (result) {
        if (result.code == 200) {
          $('tr.' + data.key + ' td a.edit').addClass('add');
          $('tr.' + data.key + ' td a.edit').removeClass('edit');
          $('tr.' + data.key + ' td.quantity').html(result.html);
        }
        if (result.code == 201) {
          $('tr.' + data.key + ' td a.add').addClass('edit');
          $('tr.' + data.key + ' td a.add').removeClass('add');
          $('tr.' + data.key + ' td.quantity').html(result.html);
        }
      }, 'json');
    },
    
    add_to_basket : function (id, quantity, product) {

      var params = {
        type : 'addToBasket',
        qty  : quantity,
        id   : id
      };

      $.post('/ajax.php', params, function (data) {
        if (data.notice) {
        } else {
          if (data.status) {
            $('#basket div.tx-commerce-pi1 span').html(data.basket);
            $('#basket div.tx-commerce-pi1 span').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
            $('tr.' + product.key + '').addClass('in-cart');
          }
          $('tr.' + product.key + ' td a.add').addClass('edit');
          $('tr.' + product.key + ' td a.add').removeClass('add');
          $('tr.' + product.key + ' td.quantity').html('');
          $('tr.' + product.key + ' td.size').html('');
        }
      }, 'json');
    }
    
  };

  $.fn.mannequin = function (method) {
    if ( methods[method] ) {
      return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.mannequin' );
    }
  };
})( jQuery );

$(function() {
  $('#mannequin').mannequin();
//  $('ul#mannequin-menu').appendTo($('nav.main li li.active')).toggle();
//  $('#mannequin div.hidden').each(function() {
//    $(this).removeClass('hidden');
//  });
  $('#mannequin #loading').remove();
});
