/**
 * @copyright bellcom open source aps
 * @author ulrik nielsen <un@bellcom.dk>
 */

(function ($) {

  var params = {};
  var layer_data = {};

  var methods = {
    init : function (options) {
      if (undefined !== options) {
        params = options;
      }

      return this.each(function () {
        // design
        methods.basket_states();

        // cache
        $('#mannequin-layers .item', this).each(function(index, elm) {
          var id = elm.id;
          var data = $(elm).data();
          data.key = elm.id;
          layer_data[id] = data;
        });

        // triggers
        $('#mannequin-layers .pane .item a').bind('click.mannequin', methods.layer_click);
        $('#mannequin-basket td.actions a').on('click.mannequin', methods.basket_item_click);
        $('#mannequin-basket td.name a').on('click.mannequin', function(event) {
          event.preventDefault();
          this.blur();
          window.open(this.href);
        });

        if ($(document).innerWidth() < 1250) {
          $('#mannequin-mini-basket').addClass('under');
        } else {
          $('#mannequin-mini-basket h4 i').remove();
        }

        $('#mannequin-mini-basket h4 i').on('click', function(event) {
          event.preventDefault();
          var $i = $(this);
          var $elm = $(this).closest('div');
          var $h4 = $('h4 span', $elm);
          var $wrapper = $('.wrapper', $elm);

          var title = $i.data('other');
          $i.data('other', $i.prop('title'));

          if (1 == $elm.css('zIndex')) {
            $i.text('-');
            $i.css('fontSize', '1.4em');
            $elm.addClass('bg');
            $elm.animate({
              width: '230px',
              left: '-230px',
              zIndex: 10000
            }, function() {
              $h4.show();
              $wrapper.slideDown();
              $i.prop('title', title);
            });
          } else {
            $wrapper.slideUp(function() {
              $elm.css('zIndex', 1);
              $elm.animate({
                width: '190px',
                left: '-30px'
              }, function() {
                $elm.removeClass('bg');
                $i.text('+');
                $i.css('fontSize', '2.6em');
                $h4.hide();
                $i.prop('title', title);
                if ('' === $('#mannequin-basket tfoot td.total').text()) {
                  $elm.hide();
                }
              });
            });
          }

        });

        $('#mannequin-mini-basket .wrapper a').on('click', function(event) {
          event.preventDefault();

          var $this = $(this);
          var c = $this.prop('class');

          if ($this.hasClass('remove')) {
            $('#mannequin-mini-basket a').each(function(index, element) {
              var $element = $(element);
              // skip add and remove links
              if ($element.is('.remove, .add')) {
                return;
              }

              var e = $element.prop('class');
              methods.remove_doll_layer(e);
            });
            return;
          }

          if ($this.hasClass('add')) {
            $('#mannequin-basket').show();
            $.colorbox({inline:true, href:'#mannequin-basket'});
            $.colorbox.resize();
            return;
          }

          methods.remove_doll_layer(c);
        });
        $(document).bind('cbox_closed', function(){
          $('#mannequin-basket').hide();
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

      var data_id = $(this).parent().attr('id');
      var data = layer_data[data_id];

      var existing_layer = $('#mannequin-doll .mannequin-layer-' + data.layer);
      var existing_layer_data = null;
      var swap_layer = null;

      // if a layer already exist we need to remove the old, before adding the new
      if (existing_layer.length) {
        existing_layer_data = layer_data[existing_layer.attr('id')];
        $('#mannequin-doll .mannequin-layer-' + data.layer).remove();
        swap_layer = existing_layer.attr('id');
        if (existing_layer_data.image == data.image) {
          methods.remove_doll_layer(swap_layer);
        }
      }
      if (!existing_layer_data || existing_layer_data.image != data.image) {
        $('#mannequin-doll .layers').append('<img src="' + params.cdn + data.image + '" class="layer mannequin-layer-' + data.layer + '" id="' + data_id + '" style="z-index: ' + (data.layer * 10) + ';" />');
        $.post(base_url + 'mannequin/dress_form/add', data, function (result) {

          if (result.status) {
            $('#mannequin-basket tbody tr.empty').remove();

            var master = data.master;
            if (swap_layer) {
              $('#mannequin-basket tbody tr.' + swap_layer).replaceWith(result.data.html);
              $('#mannequin-mini-basket .container a.'+swap_layer).replaceWith('<a href="" class="'+data.key+'">'+master+' <span class="right">'+accounting.formatMoney(data.raw_price)+'</span></a>');
            } else {
              $('#mannequin-basket tbody').append(result.data.html);
              $('#mannequin-mini-basket div.container').prepend('<a href="" class="'+data.key+'">'+master+' <span class="right">'+accounting.formatMoney(data.raw_price)+'</span></a>');
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

      var data = layer_data[key];

      // get color, size and quantity selectors so we can let the
      // user add this product to the "real" basket
      if ($(this).hasClass('edit')) {
        data.action = 'edit';
        $.post(base_url+'mannequin/cart/get/form/size', data, function (result) {
          if (result.status) {
            $('tr.' + key + ' td.size').html(result.data.html);
            $('tr.' + key + ' td.size select').change(function() {
              methods.item_size_selected(this, data);
            });
          }
          $.colorbox.resize();
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
      var data = layer_data[key];
      data.key = key;
      data.action = $(this).attr('class ');
      $('.mannequin-layer-' + data.layer).remove();
      $('#mannequin-mini-basket a.'+key).remove();
      methods.basket_states();
    },

    basket_states : function () {
      if ($('#mannequin-basket tbody tr').length) {
        $('#mannequin-mini-basket').show();
        $('#mannequin-basket tfoot').show();

        var price = 0;
        $('#mannequin-basket tbody tr').each(function() {
          var data = layer_data[$(this).attr('class').split(' ')[0]];
          price += data.raw_price;
        });

        var total = accounting.formatMoney(price);
        $('#mannequin-basket tfoot td.total').text(total);
        $('#mannequin-mini-basket p.total').text($($('#mannequin-basket tfoot td')[0]).text()+' '+total);
      } else {
        $('#mannequin-basket tfoot td.total').text('');
        $('#mannequin-basket tfoot').hide();
        $('#mannequin-basket tbody').append('<tr class="empty"><td colspan="6">' + Translator.get('js:mannequin.empty') + '</td></tr>');

        if ($('#mannequin-mini-basket').hasClass('under')) {
          if ($('#mannequin-mini-basket').hasClass('bg')) {
            $('#mannequin-mini-basket h4 i').click();
          } else {
            $('#mannequin-mini-basket').hide();
          }
        } else {
          $('#mannequin-mini-basket').hide();
        }
      }
    },

    item_size_selected : function (elm, data) {
      data.size = $(elm).find('option:selected').val().split(':')[0];
      $.post(base_url+'mannequin/cart/get/form/quantity', data, function (result) {
        if (result.status) {
          $('tr.' + data.key + ' td a.edit').addClass('add');
          $('tr.' + data.key + ' td a.edit').removeClass('edit');
          $('tr.' + data.key + ' td.quantity').html(result.data.html);
        } else {
          $('tr.' + data.key + ' td a.add').addClass('edit');
          $('tr.' + data.key + ' td a.add').removeClass('add');
          $('tr.' + data.key + ' td.quantity').html(result.message);
        }
      }, 'json');
    },

    add_to_basket : function (id, quantity, product) {

      var params = {
        size : product.size,
        color : product.color,
        quantity : quantity,
        master : product.master
      };

      $.post(base_url+'add-to-basket', params, function (response) {
        if (response.status) {
          $.cookie('basket', response.data);
          $('#mini-basket a.total').html(response.data);
          dialoug.slideNotice(response.message);

          $('tr.' + product.key + '').addClass('in-cart');
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
})(jQuery);
