(function($) {


  var gui = (function($) {
    var pub = {}

    pub.initUI = function() {
      // open in a new window
      $('li.facebook a, a[rel="external"]').on('click', function(event) {
        event.preventDefault();
        window.open(this.href);
      });

      $("#select-domain a.open-menu").on('click', function(event) {
        event.preventDefault();
        $("#select-domain div").slideToggle();
      });

      // ios class added to body
      switch (navigator.platform) {
      case 'iPad':
      case 'iPhone':
      case 'iPod':
        $('html').addClass('ios');
        break;
      }
    }


    /**
     * js countdown
     */
     pub.initCountdown = function() {
      // frontpage count down
      var $countdown = $('td.countdown strong');
      if ($countdown) {
        $countdown.countdown({
          timezone: +1,
          until: new Date(ExposeTranslation.get('js:countdown.date')),
          layout: '<strong>' + ExposeTranslation.get('js:countdown.format') + '</strong>'
        });
      }
     }


    /**
     * animated effect on first menu level
     */
    pub.initAnimatedMenu = function() {
      $('nav.main > ul > li > a').hover(function() {
        var $this = $(this);
        var left = $this.data('pl-save');
        if (left == undefined) {
          left = parseInt($this.css('padding-left'));
          $this.data('plsave', left);
        }
        $this.animate({
          'padding-left': (left + 10) + 'px'
        }, 'fast');
      }, function() {
        var $this = $(this);
        $this.animate({
          'padding-left': $this.data('plsave') + 'px'
        }, 'fast');
      });
    }

    pub.initBasket = function() {
      /**
       * we always fetch the basket via ajax, this
       * way we can keep stuff in varnish without esi
       */
      var $basket = $('#mini-basket a');
      $.getJSON(base_url + 'miniBasket', function(data) {
        if (data.status && data.data) {
          $basket.text(data.data);
        }
      });
    };

    return pub;
  })(jQuery);

  gui.initUI();
  gui.initCountdown();
  gui.initAnimatedMenu();
  gui.initBasket();

})(document, jQuery);
