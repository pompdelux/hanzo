(function($) {

  var gui = (function($) {
    var pub = {};

    pub.initUI = function() {
      jaiks.init({'url': base_url+'rest/v1/jaiks'});

      // open in a new window
      $('li.facebook a, a[rel="external"]').on('click', function(event) {
        event.preventDefault();

        var url = this.href;
        if ($(this).parent().hasClass('facebook')) {
          url = 'https://www.facebook.com/POMPdeLUXDK';
        }
        window.open(url);
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

      // fix footer if mobile unit
      if ($('html').hasClass('touch')) {
        placeFooter();
        window.onorientationchange = function() {
          placeFooter();
        };
        $('body').bind('near-you-container.loaded', function() {
          placeFooter();
        });
      }

      // use inline labels if the "real" labels are hidden
      if ($('form.newsletter-subscription-form label').is(':hidden')) {
        $('form.newsletter-subscription-form input[title]').each(function() {
          if (this.value === '') {
            this.value = this.title;
          }
          $(this).focus(function() {
            if (this.value === this.title) {
              $(this).val('').addClass('focused');
            }
          });
          $(this).blur(function() {
            if (this.value === '') {
              $(this).val($(this).attr('title')).removeClass('focused');
            }
          });
        });
      }

      var media_files = $('a.media_file.rewrite');
      if (media_files.length) {
        var data = [];
        media_files.each(function(index, element) {
          $(element).addClass('index-'+index);
          data.push({
            index: index,
            file: element.href
          });
        });
        $.post(cdn_url+'filetime.php', {payload: {data: data}}, 'json');
      }

    };

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
        var lang = $('html').attr('lang');
        if (lang !== 'en') {
          $countdown.countdown('change', $.countdown.regional[lang]);
        }
      }
     };


    /**
     * animated effect on first menu level
     */
    pub.initAnimatedMenu = function() {
      $('nav.main > ul > li > a').hover(function() {
        var $this = $(this);
        var left = $this.data('pl-save');
        if (left === undefined) {
          left = parseInt($this.css('padding-left'), 0);
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
    };

    pub.initBasket = function() {
      /**
       * we always fetch the basket via ajax, this
       * way we can keep stuff in varnish without esi
       */
      var $basket = $('#mini-basket a');
      if ($basket.length) {

        $.ajax({
          url: base_url + 'miniBasket',
          dataType: 'json',
          cache: false,
          success: function(response) {
            if (response.status) {
              // populate mini basket
              if (response.data.total) {
                $basket.text(response.data.total);
              }
              // show "in edit" warning
              if (response.data.warning) {
                $('div#main').prepend(response.data.warning);
              }
            }
          }
        });
      }
    };

    var getDocHeight = function(){
      var D = document;
      return Math.max(Math.max(
          document.body.scrollHeight,
          document.documentElement.scrollHeight
        ),
          Math.max(document.body.offsetHeight,
          document.documentElement.offsetHeight
        ),
          Math.max(document.body.clientHeight,
          document.documentElement.clientHeight
      ));
    };

    var placeFooter = function() {
      $("footer").css({
        position : 'absolute',
        height: $('footer').height() + 'px',
        top : (getDocHeight() - $('footer').outerHeight(true)) + 'px',
        width : $('body').width()
      });
    };

    return pub;
  })(jQuery);

  gui.initUI();
  gui.initCountdown();
  gui.initAnimatedMenu();
  gui.initBasket();

})(document, jQuery);
