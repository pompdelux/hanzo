var i18n = (function($) {
  var pub = {}

  // TODO: move to controller
  var map = {
    da : {
      'Close' : 'Luk',
      'Ok' : 'Ok',
      'Cancel' : 'Annuller',
      'Notice!' : 'Bemærk!',
      'Choose' : 'Vælg',
      'Quantity' : 'Antal',
      'countdown date' : 'February 20, 2012 09:00:00',
      'countdown format' : '{d<}{dn} {dl} {d>} {hn} {hl} {mn} {ml} {sn} {sl}',
      'late delivery notice': '<p>Din ordre indeholder vare(r) vi ikke har på lager i øjeblikket. Ordren pakkes når vi har alle varer på vores lager.</p><p>Hele din ordre forventes afsendt fra vores lager senest: <strong>%date%</strong>.',
      'delete from basket warning' : 'Er du sikker på du vil slette <strong>%product%</strong> fra din kurv ?'
    },
    en : {
      'countdown date' : 'February 20, 2012 09:00:00',
      'countdown format' : '{d<}{dn} {dl} {d>} {hn} {hl} {mn} {ml} {sn} {sl}',
      'late delivery notice': '<p>Your order includes item(s) we do not have in stock at the moment. The order will be packed, when all items are back in stock. </p><p>Your order will be shipped from our warehouse at the latest: <strong>%date%</strong>.</p>',
      'delete from basket warning' : 'Are you sure you want to delete <strong>%product%</strong> from your cart?'
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
