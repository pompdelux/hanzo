/**
 * doaloug framework build around colorbox
 */
var dialoug = (function($) {
  var pub = {};

  pub.confirm = function (title, message, callback) {
    var $callback = callback;

    $.colorbox({
      'top' : '25%',
      'close' : '',
      'html': '<h2>' + title + '</h2><div class="message">' + message + '</div><div class="bottons"><a class="dialoug-confirm" data-case="ok" href="">' + t('Ok') + '</a><a class="dialoug-confirm" data-case="cancel" href="">' + t('Cancel') + '</a></div>'
    });

    $('a.dialoug-confirm').bind('click', function(event) {
      $('a.dialoug-confirm').unbind('click');
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
      'close' : i18n.close,
      'html': '<h2>' + title + '</h2><div class="message">' + message + '</div>'
    });

    if ((timeout !== undefined) && (typeof timeout == 'number')) {
      setTimeout(function() {
        $.colorbox.close();
      }, timeout);
    }
  };

  return pub;
}(jQuery));
