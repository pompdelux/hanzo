(function(document, $) {


  var product = (function($) {
    var pub = {};

    pub.initZoom = function() {
      var e = $('.productimage-large');
      e.zoom({
        'url' : e.find('a').first().attr('href'),
        'click' : function(event) {
          $.colorbox({href: this.src});
          e.mouseout();
          event.preventDefault();
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
    };

    // style guides
    pub.initStyleGuide = function() {
      $('.productimage-large a').each(function() {
        var $id = $(this).data('id');
        var $guide = $('.style-guide .'+ $id);
        if ($guide.length) {
          $guide.parent().show();
          $guide.show();
        }
      });
    };

    // tabs (description and washing instructions)
    pub.initTabs = function() {
      $("ul.tabs").tabs("div.panes > div");
    };

    return pub;
  })(jQuery);

  // zoom
  product.initZoom();
  product.initStyleGuide();
  product.initTabs();

  // icon toggler
  $('.productimage-small a').click(function(e) {
    e.preventDefault();
    product.swapImages(this);
  });

})(document, jQuery);
