(function($) {


  var gui = (function($) {
    var pub = {}

    pub.initBasket = function() {
      /**
       * only try to fetch the basket on init if the element is empty.
       */
      var $basket = $('#mini-basket a');
      if ($basket.html().trim() == '') {
        $.getJSON(base_url + 'miniBasket', function(data) {
          if (data.status && data.data) {
            $basket.text(data.data);
          }
        });
      }
    };

    return pub;
  })(jQuery);

  gui.initBasket();

})(document, jQuery);
