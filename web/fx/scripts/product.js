(function(document, $) {


  var product = (function($) {
    var pub = {};

    pub.initZoom = function() {
      var currentColor = $('.productimage-large a').data('color');
      if($('.productimage-large a').length !== 0){
        $('.productimage-large a').fullImageBox({'selector':'a[rel=full-image].color-'+currentColor});
      }

      $('.productimage-large a, a.picture-zoom').on('click', function(e){
        e.preventDefault();
        $('.productimage-large a').first().fullImageBox('open');
      });
    };

    pub.initColors = function() {
      var currentColor = $('.productimage-large a').data('color');
      $('.product-color .color-'+currentColor).addClass('current');
      $('.productimage-small a').hide();
      $('.productimage-small a.color-'+currentColor).show();

      $('a.product-color').click(function(e){
        e.preventDefault();
        var currentNumber = $('.productimage-large a').data('number');
        var currentType = $('.productimage-large a').data('type');
        if(!$(this).hasClass('current')){
          currentColor = $(this).data('color');
          $('.product-color.current').removeClass('current');
          $(this).addClass('current');

          var $swapped = $('.productimage-small a.color-'+currentColor +'.number-'+currentNumber+'.type-'+currentType);
          if($swapped.length === 0){
            $swapped = $('.productimage-small a.color-'+currentColor+'.type-'+currentType);
          }

          product.swapImages($swapped.first());

          $('.productimage-small a').hide();
          $('.productimage-small a.color-'+currentColor).show();
          product.initZoom();
        }
      });
    };

    pub.swapImages = function(current) {
      var $small = $(current);
      var $small_img = $small.find('img').first();
      var small = {
        small  : $small_img.attr('src'),
        medium : $small.data('src'),
        large  : $small.attr('href'),
        id     : $small.data('id'),
        color  : $small.data('color'),
        number : $small.data('number'),
        type   : $small.data('type'),
        alt    : $small_img.attr('alt')
      };

      var $large = $('.productimage-large a');
      var $large_img = $large.find('img').first();
      var large = {
        small  : $large.data('src'),
        medium : $large_img.attr('src'),
        large  : $large.attr('href'),
        id     : $large.data('id'),
        color  : $large.data('color'),
        number : $large.data('number'),
        type   : $large.data('type'),
        alt    : $large_img.attr('alt')
      };

      $large.data('src', small.small);
      $large.attr('href', small.large);
      $large.data('id', small.id);
      $large.data('color', small.color);
      $large.data('number', small.number);
      $large.data('type', small.type);
      $large.removeClass('color-'+large.color);
      $large.addClass('color-'+small.color);
      $large.removeClass('number-'+large.number);
      $large.addClass('number-'+small.number);
      $large.removeClass('type-'+large.type);
      $large.addClass('type-'+small.type);
      $large_img.attr('src', small.medium);
      $large_img.attr('alt', small.alt);

      $small.data('src', large.medium);
      $small.attr('href', large.large);
      $small.data('id', large.id);
      $small.data('color', large.color);
      $small.data('number', large.number);
      $small.data('type', large.type);
      $small.removeClass('color-'+small.color);
      $small.addClass('color-'+large.color);
      $small.removeClass('number-'+small.number);
      $small.addClass('number-'+large.number);
      $small.removeClass('type-'+small.type);
      $small.addClass('type-'+large.type);
      $small_img.attr('src', large.small);
      $small_img.attr('alt', large.alt);

      $('.style-guide .element').hide();
      $('.style-guide .' + small.id).show();

      product.initStyleGuide();
    };
    // style guides
    pub.initStyleGuide = function() {
      $('.productimage-large a').each(function() {
        var $id = $(this).data('id');
        var $parent = $('.style-guide');
        var $guide = $('.'+ $id, $parent);
        $parent.hide();

        if ($guide.length) {
          $parent.show();
          $guide.show();
        }
      });
    };

    // tabs (description and washing instructions)
    pub.initTabs = function() {
      $(".productdescription ul.tabs").tabs("div.panes > div");

      var $tabs = $('.body-product .deliverydescription');
      $tabs.each(function(index, element) {
        var $tab = $(element);

        $('.tabs a', $tab).on('click', function(event) {
            event.preventDefault();
            var $a = $(this);

            var return_early = false;
            if ($a.hasClass('current')) {
                return_early = true;
            }

            $('.tabs a', $tab).removeClass('current');
            $('.panes div', $tab).addClass('off');

            if (return_early) {
                return;
            }

            $a.addClass('current');
            $('.pane-'+$a.data('pane'), $tab).removeClass('off');
        });
      });

    };

    // make a slideshow out of all product images.
    pub.initSlideshow = function() {
      var images = [];
      $('.productimage-small a').each(function() {
        images.push(this.href);
      });

      var contailer = '';
      for (var i=0; i<images.length; i++) {
        contailer += '<a href="'+images[i]+'" rel="slideshow"></a>';
      }
      $('#colorbox-slideshow').append(contailer);
      $('#colorbox-slideshow a').colorbox({
        rel:'slideshow',
        previous: '««',
        next: '»»',
        close: 'x',
        current: '{current} / {total}'
      });
    };

    // handle "add to basket"
    pub.initPurchase = function() {
      _resetForm();
      var in_progress = false;
      $('form.buy select.size, form.buy select.color').on('change', function() {

        if (in_progress) {
          return;
        }
        in_progress = true;

        var name = this.name;
        var value = this.value;

        dialoug.loading($(this));

        // make shure the form is updated!
        if ((name === 'size') && (value !== '')) {
          _resetForm(name);
        }

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
              dialoug.alert(Translator.get('js:notice'), response.message);
            }
            return;
          }

          if (undefined === response.data.products) {
            $('div', $form).replaceWith(Translator.get('js:out.of.stock'));
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
              if (response.force_login) {
                window.location.href = base_url+'login?f=true';
              }

              if (undefined !== response.data.location) {
                window.location.reload(true);
                return false;
              }

              if (response.message) {
                dialoug.alert(Translator.get('js:notice'), response.message);
              }
            } else {
              window.scrollTo(window.scrollMinX, window.scrollMinY);
              $('#mini-basket a.total').html(response.data);

              var $mega_basket = $('#mega-basket'),
                  $mega_basket_table = $('.basket-table-body .content', $mega_basket);
              if ($mega_basket.length) {
                // Add the new product to the basket table.
                $mega_basket_table.append('<div class="item new"><img src="' + $('.productimage-large img').attr('src') + '" />' + $('h1.title').text() + '<span class="right total">' + response.latest.price + '</span></div>');
                // Update total price.
                var item_count_regex = /\([0-9+]\) /;
                var total = response.data.replace(item_count_regex, '');
                $('.grand-total', $mega_basket).text(total);
                $('.item-count', $mega_basket).text(response.data.match(item_count_regex));

                $('body').trigger('basket_product_added');
              } else {
                dialoug.slideNotice(response.message, undefined, '#mini-basket');
              }
            }
            _resetForm();
          });

          lookup.fail(function() {
            dialoug.error(Translator.get('js:notice!'), Translator.get('js:an.error.occurred'));
          });
        } else {
          dialoug.notice(Translator.get('js:form.buy.choose.first'), 'error',3000, $('.button', $form).parent());
        }
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
          $(this).text($(this).data('text') + ' (' + Translator.get('js:out.of.stock') + ')');
        }
      });
    };

    return pub;
  })(jQuery);

  product.initZoom();
  product.initColors();
  product.initStyleGuide();
  product.initTabs();
  product.initSlideshow();
  product.initPurchase();
  product.initLastSeen();

  // icon toggler
  $('.productimage-small a').click(function(e) {
    e.preventDefault();
    product.swapImages(this);
  });

})(document, jQuery);
