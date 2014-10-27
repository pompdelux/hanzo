/**
 * dialoug framework build around colorbox
 */
var dialoug = (function($) {
  var pub = {};
  var loading_status = false;

  var templates = {
    'alert' : '<div class="dialoug alert %type%"><h2>%title%</h2><p class="message">%message%</p></div>',
    'confirm' : '<div class="dialoug confirm"><h2>%title%</h2><div class="message">%message%</div><div class="buttons"><a class="button right dialoug-confirm btn btn-success btn-sm" data-case="ok" href="">%ok%</a> <a class="button left dialoug-confirm btn btn-danger btn-sm" data-case="cancel" href="">%cancel%</a></div></div>',
    'notice' : '<div id="dialoug-message" class="dialoug-message %type%"><p>%message%</p></div>'
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
    defaults.closeButton = false;
    defaults.escKey = false;
    defaults.html = templates.confirm
      .replace('%title%', title)
      .replace('%message%', message)
      .replace('%ok%', Translator.trans('ok'))
      .replace('%cancel%', Translator.trans('cancel'))
    ;

    // fix scroll issue in ie.
    if ($('html').hasClass('ie')) {
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
      'close' : Translator.trans('close'),
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

    if ($notice.length && !$('body').hasClass('is-mobile')) {
      $notice.after(tpl);
    } else {
      if ($main.is('input')) {
        $main.before(tpl);
        $main = $main.parent();
      } else {
        $main.prepend(tpl);
      }
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
   * popup notice with no way of closing
   *
   * @param  string title   title of the message
   * @param  string message the message
   */
  pub.blockingNotice = function(title, message) {
    $.colorbox({
      'top' : '25%',
      'maxWidth' : '500px',
      'close' : '',
      'overlayClose' : false,
      'closeButton': false,
      'escKey' : false,
      'html': templates.alert
                       .replace('%title%', title)
                       .replace('%message%', message)
                       .replace('%type%', 'notice')
    });
  };


  /**
   * Slide in notification
   *
   * @param string message
   * @param int duration
   * @param mixed selector string/jQuery
   */
  pub.slideNotice = function(message, duration, selector) {

    var is_mobile = $('body').hasClass('is-mobile');

    if (undefined === duration) {
      duration = 2000;
    }
    if (typeof(selector) === 'string') {
      selector = $(selector);
    }

    if(undefined === selector){
       selector = $('body');
    }

    $('body').append('<div id="slide-notice-box" class="slide-notice-box"><div>' + message + '</div></div>');
    var $slide = $('#slide-notice-box');

    var offset = selector.offset();
    var slideWidth = $slide.outerWidth();

    // Desktops slides in from right.
    if(is_mobile === false){
      $('html,body').animate({ scrollTop : 0 });

      $('div', $slide).css({
        width : $slide.innerWidth()
      });

      $slide.css({
        left: (offset.left - 17),
        width: 0
      });

      $slide.animate({
        width: (slideWidth),
        left: (offset.left - (slideWidth + 17))
      }).delay(duration).animate({
        left: offset.left - 17,
        width: 0
      }, function() {
        $slide.remove();
      });

      return;
    }

    // Mobile devices slide down from top in fixed view.
    $slide.hide();
    $slide.slideDown(300, function(){
      $slide.delay(duration).slideUp(400, function() {
        $slide.remove();
      });
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

      var tpl = '<div class="dialoug-loading"><div>' + msg + '</div></div>';
      if (selector === 'body') {
        tpl = '<div class="dialoug-loading fullscreen"><div>' + msg + '</div></div>';
      }

      if (undefined === position) {
        $this.after(tpl);
      }
      else if(position == 'append') {
        $this.append(tpl);
      }
      else {
        $this.before(tpl);
      }
      loading_status = true;
    });
    return true;
  };


  /**
   * Stop any loading anims.
   *
   * @see dialoug.loading()
   */
  pub.stopLoading = function() {
    if (loading_status) {
      $('.dialoug-loading').remove();
      loading_status = false;
    }
  };

  sleep = function(delay) {
    var start = new Date().getTime();
    while (new Date().getTime() < start + delay) {}
  };

  return pub;
}(jQuery));
