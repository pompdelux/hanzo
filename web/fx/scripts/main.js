(function($) {


  var gui = (function($) {
    var pub = {}

    pub.initUI = function() {
      // open facebook in a new window
      $('li.facebook a').click(function(event) {
        event.preventDefault();
        window.open(this.href);
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
          until: new Date(i18n.t('countdown date')),
          layout: '<strong>' + i18n.t('countdown format') + '</strong>'
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

  gui.initUI();
  gui.initCountdown();
  gui.initAnimatedMenu();
  gui.initBasket();

})(document, jQuery);
