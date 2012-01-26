/**
 * dialoug framework build around colorbox
 */
var dialoug = (function($) {
  var pub = {};

  var templates = {
    'alert' : '<div class="dialoug alert %type%"><h2>%title%</h2><p class="message">%message%</p></div>',
    'confirm' : '<div class="dialoug confirm"><h2>%title%</h2><div class="message">%message%</div><div class="buttons"><a class="button right dialoug-confirm" data-case="ok" href="">%ok%</a><a class="button left dialoug-confirm" data-case="cancel" href="">%cancel%</a></div></div>',
    'notice' : '<div id="dialoug-message %type%"><p>%message%</p></div>'
  };

  pub.confirm = function(title, message, callback) {
    var $callback = callback;

    $.colorbox({
      'top' : '20%',
      'close' : '',
      'maxWidth' : '400px',
      'overlayClose' : false,
      'escKey' : false,
      'html' : templates.confirm
                        .replace('%title%', title)
                        .replace('%message%', message)
                        .replace('%ok%', i18n.t('Ok'))
                        .replace('%cancel%', i18n.t('Cancel'))
    });

    $('#cboxContent .dialoug a').bind('click', function(event) {
      $('#cboxContent .dialoug a').unbind('click');
      event.preventDefault();

      $.colorbox.close();

      if (undefined !== $callback) {
        $callback($(this).data('case'));
      }
    });
  };

  pub.alert = function(title, message, type, timeout) {
    if (undefined === type) {
      type = 'info';
    }

    $.colorbox({
      'top' : '25%',
      'maxWidth' : '400px',
      'close' : i18n.t('Close'),
      'html': templates.alert
                       .replace('%title%', title)
                       .replace('%message%', message)
                       .replace('%type%', type)
    });

    if ((undefined !== timeout) && (typeof timeout == 'number')) {
      setTimeout(function() {
        $.colorbox.close();
      }, timeout);
    }
  };

  /**
   * wrapper functions for the different alert types.
   * type can be:
   * - info
   * - warning
   * - error
   */
  pub.info = function (title, message, timeout) {
    pub.alert(title, message, 'info', timeout);
  }
  pub.warning = function (title, message, timeout) {
    pub.alert(title, message, 'warning', timeout);
  }
  pub.error = function (title, message, timeout) {
    pub.alert(title, message, 'error', timeout);
  }

  pub.notice = function(message, type, timeout) {
    var $main = $('#main');
    var $notice = $('h1', $main);

    type = undefined === type ? 'info' : type;
    var tpl = templates.notice
                       .replace('%message%', message)
                       .replace('%type%', type)
    ;

    if ($notice.length) {
      $notice.after(tpl);
    }
    else {
      $main.prepend(tpl);
    }

    var $message = $('div#dialoug-message', $main);
    var dim = $message.offset();
    $(document).scrollTop(dim.top - ($message.height() + 50));

    $message.slideDown();

    if (undefined !== timeout && $.isNumeric(timeout)) {
      setTimeout(function() {
        $message.slideUp(function() {
          $message.remove();
        });
      }, timeout);
    }
  };

  pub.slideNotice = function(message, duration) {
    if (undefined === duration) {
      duration = 2000;
    }

    $('body').prepend('<div id="slide-notice-box">' + message + '</div>');
    var $slide = $('#slide-notice-box');
    var slideWidth = $slide.outerWidth();
    var docWidth = $(document).width();

    $slide.css({
      left: docWidth + 'px',
      width: slideWidth
    });

    $slide.animate({
      left: '-=' + (slideWidth + 10) + 'px'
    }, {
      complete: function() {
        sleep(duration);
        $slide.animate({
          left: docWidth
        }, {
          complete: function() {
            $slide.remove();
          }
        });
      }
    });
  };


  var loading_status = false;
  pub.loading = function(selector, message, position) {
    if (loading_status) { return; }

    $(selector).each(function() {
      var $this = $(this);
      var msg = undefined === message ? '' : message;

      if (undefined === position) {
        $this.after('<div class="dialoug-loading">' + msg + '</div>');
      } else {
        $this.before('<div class="dialoug-loading">' + msg + '</div>');
      }
      loading_status = $('.dialoug-loading', $this.parent());
    });
  }

  pub.stopLoading = function() {
    if (loading_status) {
      loading_status.remove();
      loading_status = false;
    }
  }

  sleep = function(delay) {
    var start = new Date().getTime();
    while (new Date().getTime() < start + delay);
  }

  return pub;
}(jQuery));
