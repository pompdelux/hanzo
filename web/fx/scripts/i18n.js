  var i18n = (function($) {
    var pub = {}

    var map = {
      da : {
        'Close' : 'Luk',
        'Ok' : 'Ok',
        'Cancel' : 'Annuller',
        'Notice!' : 'Bemærk!'
      }
    };

    pub.t = function(key, params) {
      var msg = map[$('html').attr('lang')][key];
      if (msg === undefined) {
        return key;
      }

      /**
       * replace substitutions if any
       */
      if (params !== undefined) {
        $.each(params, function(x, y) {
          msg = msg.replace(new RegExp(x, 'g'), y);
        });
      }
      return msg;

    };

    return pub;
  })(jQuery);

