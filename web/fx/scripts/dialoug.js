/**
 * dialoug framework build around colorbox
 */
var dialoug = (function($) {
  var pub = {};

  pub.confirm = function (title, message, callback) {
    var $callback = callback;

    $.colorbox({
      'top' : '25%',
      'close' : '',
      'html': '<div class="dialoug confirm"><h2>' + title + '</h2><p class="message">' + message + '</p><div class="buttons"><a class="button right dialoug-confirm" data-case="ok" href="">' + i18n.t('Ok') + '</a><a class="button left dialoug-confirm" data-case="cancel" href="">' + i18n.t('Cancel') + '</a></div></div>'
    });

    $('#cboxContent .dialoug a').bind('click', function(event) {
      $('#cboxContent .dialoug a').unbind('click');
      event.preventDefault();
      $.colorbox.close();
      if ($callback !== undefined) {
        $callback($(this).data('case'));
      }
    });
  };

  pub.alert = function (title, message, timeout) {
    $.colorbox({
      'top' : '25%',
      'close' : i18n.t('Close'),
      'html': '<div class="dialoug alert"><h2>' + title + '</h2><p class="message">' + message + '</p></div>'
    });

    if ((timeout !== undefined) && (typeof timeout == 'number')) {
      setTimeout(function() {
        $.colorbox.close();
      }, timeout);
    }
  };


  pub.slideNotice = function (message, duration) {
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

  sleep = function (delay) {
    var start = new Date().getTime();
    while (new Date().getTime() < start + delay);
  }

  return pub;
}(jQuery));
