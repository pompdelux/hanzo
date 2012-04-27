(function(document, $) {


  var product = (function($) {
    var pub = {};

    pub.initZoom = function() {
      var e = $('.productimage-large');
      e.zoom({
        url : e.find('a').first().attr('href'),
        callback: function(){
          $(this).colorbox({href: this.src, close: 'x'});
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
        id     : $small.data('id')
      };

      var $large = $('.productimage-large a');
      var $large_img = $large.find('img').first();
      var large = {
        small  : $large.data('src'),
        medium : $large_img.attr('src'),
        large  : $large.attr('href'),
        id     : $large.data('id')
      };

      $large.data('src', small.small);
      $large.attr('href', small.large);
      $large.data('id', small.id);
      $large_img.attr('src', small.medium);

      $small.data('src', large.medium);
      $small.attr('href', large.large);
      $small.data('id', large.id);
      $small_img.attr('src', large.small);

      $('.style-guide .element').hide();
      $('.style-guide .' + small.id).show();

      product.initZoom();
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
      $("ul.tabs").tabs("div.panes > div");
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
      $('form.buy select#size, form.buy select#color').on('change', function() {
        var name = this.name;
        var value = this.value;

        // make shure the form is updated!
        if ((name === 'size') && (value !== '')) {
          _resetForm(name);
        }

        var $form = $('form.buy');
        $.ajax({
          url: base_url + 'rest/v1/stock-check',
          dataType: 'json',
          data: $form.serialize(),
          async: false,
          success: function(responce, textStatus, jqXHR) {
            if (false === responce.status) {
              if (responce.message) {
                dialoug.alert(ExposeTranslation.get('js:notice'), responce.message);
              }
              return;
            }

            // populate color select with options
            if (name === 'size') {
              $.each(responce.data.products, function(index, product) {
                $('form.buy #color').append('<option value="'+product.color+'">'+product.color+'</option>');
              });
              $('form.buy #color').closest('label').removeClass('off');
            }

            if (name == 'color') {
              var product = responce.data.products[0];
              if (product.date) {
                dialoug.confirm(ExposeTranslation.get('js:notice'), responce.message, function(c) {
                  if (c == 'ok') {
                    $('form.buy #quantity').closest('label').removeClass('off');
                    $form.find('.button').show();
                    $form.append('<input type="hidden" name="date" value="' + product.date + '">');
                  }
                });
              }
              else {
                $('form.buy #quantity').closest('label').removeClass('off');
                $form.find('.button').show();
              }
            }
          }
        });
      });

      $('form.buy').on('submit', function(event) {
        event.preventDefault();

        var $form = $(this);
        $.ajax({
          url: $form.attr('action'),
          dataType: 'json',
          type: 'POST',
          data: $form.serialize(),
          async: false,
          success: function(responce, textStatus, jqXHR) {
            if (false === responce.status) {
              if (responce.message) {
                dialoug.alert(ExposeTranslation.get('js:notice', responce.message));
              }
            }
            else {
              window.scrollTo(window.scrollMinX, window.scrollMinY);
              $('#mini-basket a').html(responce.data);
              dialoug.slideNotice(responce.message);
            }
            _resetForm();
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(ExposeTranslation.get('js:notice!'), ExposeTranslation.get('js:an.error.occurred'));
          }
        });
      });

    };

    _resetForm = function(section) {
      var $this = $('form.buy');

      if ( section !== 'size' ) {
        $this.find('#size option').each(function(index) {
          $(this).removeProp('selected');
        });
      }

      $this.find('#color option').each(function(index) {
        if (this.value !== ''){
          $(this).remove();
        }
      });
      $this.find('label').each(function() {
        if (this.htmlFor !== 'size') {
          $(this).addClass('off');
        }
      });
      $this.find('#quantity option').each(function(index) {
        $(this).removeProp('selected');
      });
      $this.find('.button').hide();
    };

    return pub;
  })(jQuery);

  product.initZoom();
  product.initStyleGuide();
  product.initTabs();
  product.initSlideshow();
  product.initPurchase();

  // icon toggler
  $('.productimage-small a').click(function(e) {
    e.preventDefault();
    product.swapImages(this);
  });

})(document, jQuery);
