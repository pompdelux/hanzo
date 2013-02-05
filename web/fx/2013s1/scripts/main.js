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
        var payload = {data:[]};
        media_files.each(function(index, element) {
          $(element).addClass('index-'+index);
          payload.data.push({
            index: index,
            file: element.href
          });
        });

        var xhr = $.ajax({
          url: cdn_url+'filetime.php',
          dataType: 'jsonp',
          data: jQuery.param(payload)
        });

        xhr.done(function (response) {
          $.each(response.data, function (i, element) {
            if (undefined !== element.mtime) {
              var date = new Date(element.mtime);
              date = date.getUTCDate()+'/'+date.getUTCMonth()+'/'+date.getUTCFullYear()+' '+date.getUTCHours()+':'+date.getUTCMinutes();
              var $elm = $('a.media_file.index-'+element.index);
              var $em = $elm.next('em');
              var label = $elm.data('datelabel');
              $em.text(label.replace('%date%', date)).css('display', 'block');
            }
          });
        });
      }

      // menu handeling
      var $menu = $('nav.main-menu');
      $('> ul > li', $menu).each(function(index, element) {
        var $element = $(element);
        var subs = $('ul > li > ul > li', $element).length;
        if (subs) {
          var l = $('ul > li > ul', $element).length;
          l = (l * 218);

          if (subs < 30) {
            l = l / 2;
          }

          $('ul > li', $element).css({ float:'left', width: '200px' });
          $('> ul', $element).css({ width: l });
        } else {
          $('ul > li', $element).css({ display:'block' });
        }
      });
      $('> ul > li > a', $menu).click(function(event) {
        var $this = $(this).parent();
        var $element = $('> ul', $this);

        $('> ul > li > ul.on', $menu).not($element).removeClass('on');
        $element.toggleClass('on');

        if ($('ul', $this).length) {
          event.preventDefault();
        }
      });
    };

    /**
     * js countdown
     */
     pub.initCountdown = function() {
      // frontpage count down
      var $countdown = $('.countdown');

      if ($countdown) {
        $countdown.countdown({
          timezone: +1,
          until: new Date(Translator.get('js:countdown.date')),
          layout: '<span>' + Translator.get('js:countdown.format') + '</span>'
        });
        var lang = $('html').attr('lang');
        if (lang !== 'en') {
          $countdown.countdown('change', $.countdown.regional[lang]);
        }
      }
     };

    pub.initBasket = function() {
      var $basket = $('#mini-basket a');
      if ($basket.length) {

        $.cookie.defaults = {
          domain : cookie_params.domain,
          path : cookie_params.path
        };

        var basket = $.cookie('basket');
        if (basket) {
          $basket.text(basket);
        }

        var notice = $.cookie('__ice_n');
        if (notice) {
          $('div#main').prepend(notice);
        }
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

    return pub;
  })(jQuery);

  gui.initUI();
  gui.initCountdown();
  gui.initBasket();

})(document, jQuery);
