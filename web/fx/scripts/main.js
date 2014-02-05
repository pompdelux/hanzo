(function($) {
  var gui = (function($) {
    var pub = {};

    pub.initUI = function() {
      jaiks.init({'url': base_url+'rest/v1/jaiks'});

      $("#select-domain a.open-menu").on('click', function(event) {
        event.preventDefault();
        $("#select-domain div").slideToggle();
      });

      // ios class added to body
      switch (navigator.platfom) {
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
              var date = element.mtime;
              var $elm = $('a.media_file.index-'+element.index);
              var $em = $elm.next('em');
              var label = $elm.data('datelabel');
              $em.text(label.replace('%date%', date)).css('display', 'block');
              $elm.attr('href', $elm.attr('href')+'?'+element.ts);
            }
          });
        });
      }

      // externals opened in new window
      $('a.js-external').on('click', function(event) {
        event.preventDefault();
        window.open(this.href);
      });

      // menu handeling
      if (false === $('body').hasClass('is-mobile')) {
        var $menu = $('nav.main-menu');

        // set parent li's class to active for active elements.
        $('nav.first.main-menu .active')
          .parents('li')
          .toggleClass('active inactive')
        ;

        var menu_width = 0;
        $('li li.heading', $menu).each(function(index, element) {
          var $element = $(element);
          var tmp_width = $element.width() - 40;
          if (menu_width < tmp_width) {
            menu_width = tmp_width;
          }

          $element.addClass('floaded');
        });
        $('li li.heading', $menu).closest('ul').each(function(index, element) {
          var $element = $(element);
          var count = $('> li', $element).length;
          $element.css('width', (menu_width * count) + 5);
        });


        $('> ul > li > a', $menu).click(function(event) {
          var $this = $(this).parent();
          var $element = $('> ul', $this);

          if ($('html').hasClass('touch') && $element.hasClass('on') === false && $element.length) {
            if ((!navigator.userAgent.match(/iPhone/i)) && (!navigator.userAgent.match(/iPod/i)) && (!navigator.userAgent.match(/iPad/i))) {
              event.stopPropagation();

              $('> ul > li > ul.on', $menu).removeClass('on');
              $element.toggleClass('on');

              event.preventDefault();
            }
          }
        });

        $('html').click(function(event) {
          $('.on', $menu).removeClass('on');
        });
      }

      // handeling mobile->pc->mobile view switching
      $.cookie.defaults = {
        domain : cookie_params.domain,
        path : cookie_params.path
      };

      var mode = $('body').hasClass('is-mobile') ? 'mobile' : 'pc';
      var choice = mode;
      var fixed_mode = false;

      if ($.cookie('X-UA-Device-force')) {
        choice = $.cookie('X-UA-Device-force');
        fixed_mode = true;
      }

      if (fixed_mode && (mode == 'pc')) {
        $('footer').before('<div class="container container_8"><a href="" title="Gå til den mobile version af siden" class="switch-site-view">Mobil version</a></div>');
      }

      $('.switch-site-view').on('click', function(event) {
        event.preventDefault();
        if ((choice == 'mobile')) {
          $.cookie('X-UA-Device-force', 'pc');
        } else {
          $.removeCookie('X-UA-Device-force');
        }
        document.location.href = document.location.href;
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
          until : new Date(Translator.get('js:countdown.date')),
          layout : '<span>' + Translator.get('js:countdown.format') + '</span>',
          timezone : +1,
          serverSync : function () {
            var time = null;
            $.ajax({url: '/tools.php?action=timestamp',
              async: false, dataType: 'text',
              success: function(text) {
                time = new Date(text);
              }, error: function(http, message, exc) {
                time = new Date();
            }});
            return time;
          }
        });

        var lang = $('html').attr('lang');
        $countdown.countdown('option', $.countdown.regional[lang]);
        $.countdown.setDefaults($.countdown.regional['']);
      }
    };

    pub.initBasket = function() {
      var $basket = $('#mini-basket a.total');
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

    pub.initToTop = function() {
      $('.to-top').on('click', function(e){
        e.preventDefault();
        $("html, body").animate({
          scrollTop: 0
        }, "slow");
      });
    };

    pub.initSearchForm = function() {
      var mini_basket_width = $('#secondary-links').outerWidth(),
          search_form_padding = $('form.search-form input[type="text"]').innerWidth() - $('form.search-form input[type="text"]').width();

      $('form.search-form input[type="text"]').css('width', mini_basket_width - search_form_padding);
      $('nav.first.main-menu>ul.topmenu').css('padding-right', mini_basket_width);

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
  gui.initToTop();
  gui.initSearchForm();

})(document, jQuery);
