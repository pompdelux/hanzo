/**
 * dialoug framework build around colorbox
 */
var dialoug = (function($) {
  var pub = {};
  var loading_status = false;

  var templates = {
    'alert' : '<div class="dialoug alert %type%"><h2>%title%</h2><p class="message">%message%</p></div>',
    'confirm' : '<div class="dialoug confirm"><h2>%title%</h2><div class="message">%message%</div><div class="buttons"><a class="button right dialoug-confirm" data-case="ok" href="">%ok%</a><a class="button left dialoug-confirm" data-case="cancel" href="">%cancel%</a></div></div>',
    'notice' : '<div id="dialoug-message" class="%type%"><p>%message%</p></div>'
  };


  /**
   * Display a confirmation box via colorbox
   *
   * @param string title
   * @param string message
   * @param function callback
   */
  pub.confirm = function(title, message, callback, params) {
    var $callback = callback;

    var defaults = {
      'close' : '',
      'maxWidth' : '400px'
    };

    if (undefined !== params) {
      $.extend(defaults, params);
    }

    defaults.overlayClose = false;
    defaults.escKey = false;
    defaults.html = templates.confirm
      .replace('%title%', title)
      .replace('%message%', message)
      .replace('%ok%', Translator.get('js:ok'))
      .replace('%cancel%', Translator.get('js:cancel'))
    ;

    // fix scroll issue in ie.
    if ($.browser.msie) {
      defaults.html += '<br><br>';
    }

    $.colorbox(defaults);

    $('#cboxContent .dialoug a').bind('click', function(event) {
      $('#cboxContent .dialoug a').unbind('click');
      event.preventDefault();

      $.colorbox.close();

      if (undefined !== $callback) {
        $callback($(this).data('case'));
      }
    });
  };


  /**
   * Display alert messages via colorbox
   *
   * @param string title
   * @param string message
   * @param string type
   * @param int timeout
   */
  pub.alert = function(title, message, type, timeout) {
    if (undefined === type) {
      type = 'info';
    }

    $.colorbox({
      'top' : '25%',
      'maxWidth' : '400px',
      'close' : Translator.get('js:close'),
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
  };
  pub.warning = function (title, message, timeout) {
    pub.alert(title, message, 'warning', timeout);
  };
  pub.error = function (title, message, timeout) {
    pub.alert(title, message, 'error', timeout);
  };

  /**
   * notice method, can either popup or inline notices
   *
   * @param  string message  message to display
   * @param  string type     type of message (info/warning/error)
   * @param  mixed  timeout  if set, timeout of message in miliseconds
   * @param  mixed  selector if set, selector to override default
   */
  pub.notice = function(message, type, timeout, selector) {
    var $main;

    if (selector === undefined) {
      $main = $('#main');
    } else {
      $main = $(selector);
    }

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

    var $message = $('div#dialoug-message',$main);
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


  /**
   * Slide in notification
   *
   * @param string message
   * @param int duration
   */
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

  /**
   * inserts a loading anim image and an optional text
   *
   * @see dialoug.stopLoading()
   * @param mixed selector either a css selector or a jQuery reference
   * @param string message, a massage to display along side the image
   * @param position string, how to insert the element, currently the followint is supported:
   *    after, append, before - default is after
   */
  pub.loading = function(selector, message, position) {
    if (loading_status) { return; }

    if (typeof(selector) === 'string') {
      selector = $(selector);
    }

    selector.each(function() {
      var $this = $(this);
      var msg = (undefined === message ? '' : message);
      var tpl = '<div class="dialoug-loading">' + msg + '</div>';

      if (undefined === position) {
        $this.after(tpl);
      }
      else if(position == 'append') {
        $this.append(tpl);
      }
      else {
        $this.before(tpl);
      }
      loading_status = $('.dialoug-loading', $this.parent());
    });
  };


  /**
   * Stop any loading anims.
   *
   * @see dialoug.loading()
   */
  pub.stopLoading = function() {
    if (loading_status) {
      loading_status.remove();
      loading_status = false;
    }
  };

  sleep = function(delay) {
    var start = new Date().getTime();
    while (new Date().getTime() < start + delay) {}
  };

  return pub;
}(jQuery));
