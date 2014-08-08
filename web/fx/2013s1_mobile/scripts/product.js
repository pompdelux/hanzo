(function(document, $) {


  var product = (function($) {
    var pub = {};

    pub.initColors = function() {
      if (typeof product_images !== "undefined") {
        var currentColor = $('.productimage-large a').data('color');
        $('.product-color.color-'+currentColor).addClass('current');

        $('a.product-color').click(function(e){
          e.preventDefault();
          var currentNumber = $('.productimage-large a').data('number');
          var currentType = $('.productimage-large a').data('type');
          if(!$(this).hasClass('current')){
            currentColor = $(this).data('color').replace(/-/g, '_');
            $('.product-color.current').removeClass('current');
            $(this).addClass('current');

            var swapped = null;
            for (var i = product_images[currentColor].length - 1; i >= 0; i--) {
              if (product_images[currentColor][i].number == currentNumber &&
                  product_images[currentColor][i].type == currentType) {
                swapped = product_images[currentColor][i];
                break;
              }
            }

            if(!swapped){
              swapped = product_images[currentColor][0];
            }

            product.swapWith(swapped);
          }
        });
      }
    };

    pub.swapWith = function(swap, prepend) {
      var $large = $('.productimage-large a');
      var $large_img = $large.find('img').first();

      // Save the current images data.
      // To be inserted into array.
      var large = {
        small_url  : $large.data('src'),
        big_url : $large_img.attr('src'),
        id     : $large.data('id'),
        color  : $large.data('color'),
        number : $large.data('number'),
        type   : $large.data('type'),
        alt    : $large_img.attr('alt')
      };

      // Set img to new image.
      $large.data('src', swap.small_url);
      $large.data('id', swap.id);
      $large.data('color', swap.color);
      $large.data('number', swap.number);
      $large.data('type', swap.type);
      $large_img.attr('src', swap.big_url);

      // Remove the swapped image from array.
      var position = product_images[swap.color.replace(/-/g, '_')].indexOf(swap);
      product_images[swap.color.replace(/-/g, '_')].splice(position, 1);

      // Add old image to the end of the array.
      if(undefined === prepend) {
        product_images[large.color.replace(/-/g, '_')].push(large);
      } else {
        product_images[large.color.replace(/-/g, '_')].unshift(large);
      }
      product.initStyleGuide();
    };

    // style guides
    pub.initStyleGuide = function() {
      $('.productimage-large a').each(function() {
        var id = $(this).data('id');
        var $parent = $('.style-guide');
        var $guide = $('.image-id-'+ id, $parent);
        $parent.hide();

        if ($guide.length) {
          $parent.show();
          $guide.show();
        }
      });
    };

    // tabs (description and washing instructions)
    pub.initTabs = function() {
      if ($('#accordion').length) {
        $('#accordion .pane').slideUp();
        $('#accordion h4').on('click', function(e){
          var $pane = $(this).next('.pane'),
              $clicker = $(this);
          if ($(this).hasClass('current')) {
            $pane.slideUp('slow', function() {
              $clicker.removeClass('current');
            });
          } else {
            $clicker.addClass('current');
            $pane.slideDown('slow');
          }
        });
      }
    };

    // Make a slideshow out of all product images.
    pub.initSlideshow = function() {
      $('#slideshow .productimage-large').on('click', function (e) {
        e.preventDefault();
      });

      $('#slideshow .navigation a').on('click', function (e) {
        e.preventDefault();
        var currentColor = $('.productimage-large a').data('color').replace(/-/g, '_');
        var swap;
        if ($(this).hasClass('prev')) {
          swap = product_images[currentColor].slice(-1)[0];
          product.swapWith(swap, 'prepend');
        } else {
          swap = product_images[currentColor][0];
          product.swapWith(swap);
        }
      });
      $('#slideshow .productimage-large').hammer().on('swipeleft', function (e) {
        var currentColor = $('.productimage-large a').data('color').replace(/-/g, '_');
        var swap;
        swap = product_images[currentColor].slice(-1)[0];
        product.swapWith(swap, 'prepend');
      });
      $('#slideshow .productimage-large').hammer().on('swiperight', function (e) {
        var currentColor = $('.productimage-large a').data('color').replace(/-/g, '_');
        var swap;
        swap = product_images[currentColor][0];
        product.swapWith(swap);
      });
    };

    // handle "add to basket"
    pub.initPurchase = function() {
      _resetForm();
      var in_progress = false;

      var select_event_type = ($('html').hasClass('ios')) ? 'blur' : 'change';

      $('form.buy select.size, form.buy select.color').on(select_event_type, function() {

        if (in_progress) {
          return;
        }

        var name = this.name;
        var value = this.value;


        // make shure the form is updated!
        if ((name === 'size')) {
          _resetForm(name);
        }

        if (value === "") {
          return;
        }

        in_progress = true;

        dialoug.loading($(this));

        var $form = $(this).closest('form');

        var lookup = $.ajax({
          url: base_url + 'stock-check',
          dataType: 'json',
          data: $form.serialize(),
          cache: false
        });

        lookup.done(function(response) {
          if (false === response.status) {
            if (response.message) {
              dialoug.alert(Translator.trans('notice'), response.message);
            }
            return;
          }

          if (undefined === response.data.products) {
            if ('size' === name) {
              $('div', $form).replaceWith(Translator.trans('out.of.stock'));
            } else {
              dialoug.notice(Translator.trans('variant.out.of.stock', {
                name : $('h1').text(),
                dimensions : $('.size option:selected', $form).text()+' '+value
              }), 'error', 6000, 'label[for="color"]');
                _resetColor();
            }
            return;
          }

          // populate color select with options
          if (name === 'size') {
            _resetColor();
            $.each(response.data.products, function(index, product) {
              var $option = $('select.color option[value="' + product.color + '"]', $form);
              if ($option.length) {
                $option.prop("disabled", false).text($option.data('text'));
              } else {
                $('select.color', $form).append('<option value="'+product.color+'" data-value="'+product.color+'">'+product.color+'</option>');
              }
            });
            $('select.color', $form).prop("disabled", false);
          }

          if (name == 'color') {
            var product = response.data.products[0];
            if (product.date) {
              dialoug.confirm(Translator.get('js:notice'), response.message, function(c) {
                if (c == 'ok') {
                  $('select.quantity', $form).closest('label').removeClass('off');
                  $form.append('<input type="hidden" name="date" value="' + product.date + '">');
                }
              });
            }
            else {
              $('select.quantity', $form).closest('label').removeClass('off');
            }
          }
        });

        lookup.always(function() {
          dialoug.stopLoading();
          in_progress = false;
        });

        lookup.fail(function (jqXHR, textStatus) {/* todo: implement failure handeling */});
      });

      $('form.buy').on('submit', function(event) {
        event.preventDefault();

        var $form = $(this);
        if ($('select.size', $form).val() && $('select.color', $form).val() && $('select.quantity', $form).val()){
          var lookup = $.ajax({
            url: $form.attr('action'),
            dataType: 'json',
            type: 'POST',
            data: $form.serialize(),
            async: false
          });

          lookup.done(function(response) {
            if (false === response.status) {
              if (undefined !== response.data.location) {
                window.location.reload(true);
                return false;
              }

              if (response.message) {
                dialoug.alert(Translator.trans('notice'), response.message);
              }
            } else {
              $('#mini-basket a').html(response.data);
              dialoug.slideNotice(response.message, undefined, '.container > header');
            }
            _resetForm();
          });

          lookup.fail(function() {
            dialoug.error(Translator.trans('notice!'), Translator.trans('an.error.occurred'));
          });
        } else {
          dialoug.notice(Translator.trans('form.buy.choose.first'), 'error',3000, $('.options', $form));
        }
      });

      $('.show-size-guide').on('click', function (e) {
        e.preventDefault();
        $(this).find('img').toggle('slow');
      });
    };


    /**
     * track products the visitor has last seen.
     * currently we track the latest 10 products.
     */
    pub.initLastSeen = function() {
      if($('#body-product input#master').length) {
        var data = $.cookie('last_viewed');

        if (data) {
          data = JSON.parse(data);
        } else {
          data = { images:[], keys:[] };
        }

        var id = $('input#master').val().replace(/[^a-z0-9]+/gi, '');

        if (-1 === $.inArray(id, data.keys)) {
          data.images.push({
            title : $('h1').text(),
            url   : document.location.href,
            image : $('.productimage-large a').data('src')
          });

          data.keys.push(id);

          if (data.keys.length > 4) {
            data.keys.shift();
            data.images.shift();
          }

          $.cookie('last_viewed', JSON.stringify(data));
        }

        $.each(data.images, function(index, data) {
          $('.latest-seen-poducts').append('<a href="'+data.url+'"><img src="'+data.image+'" alt="'+data.title+'"></a>');
        });

        // fallback option, hide container if empty
        if (0 === data.keys.length) {
          $('.latest-seen-poducts').hide();
        }
      }
    };


    var _resetForm = function(section) {
      var $this = $('form.buy');

      if ( (section !== undefined) && (section !== 'size') ) {
        $this.find('select.size option').each(function(index) {
          $(this).prop('disabled', false);
        });
      }

      $this.find('select.color option').each(function(index) {
        if (this.value !== ''){
          $(this).prop('disabled', true);
        }
      });

      $this.find('label').each(function() {
        if (this.htmlFor === 'quantity') {
          $(this).addClass('off');
        }
      });

      $this.find('select.quantity option').each(function(index) {
        $(this).prop('disabled', false);
      });
      $('select.quantity option:first', $this).prop('selected', true);

      if (section === undefined) {
        $('select.size option:first', $this).prop('selected', true);
        $('select.color option:first', $this).prop('selected', true);
        _resetColor();
      }
    };

    var _resetColor = function() {
      var $this = $('form.buy');
      $('select.color', $this).prop('disabled', true);
      $('select.color option:first', $this).prop('selected', true);
      $('select.color option', $this).each(function(index) {
        if (this.value !== ''){
          $(this).prop('disabled', true);
          if (!$(this).data('text')) {
            $(this).data('text', $(this).text());
          }

          $(this).text($(this).data('text') + ' (' + Translator.trans('out.of.stock') + ')');
        }
      });
    };

    return pub;
  })(jQuery);

  if ($('body').hasClass('body-product')) {
    product.initColors();
    product.initStyleGuide();
    product.initTabs();
    product.initSlideshow();
    product.initPurchase();
    product.initLastSeen();
  }
})(document, jQuery);
