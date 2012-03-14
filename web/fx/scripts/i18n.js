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
      'Please wait': 'Vent venligst',
      'Your action was completed': 'Din handling blev udført',
      'An error occurred': 'Der opstod en fejl',
      'countdown date' : 'February 20, 2012 09:00:00',
      'countdown format' : '{d<}{dn} {dl} {d>} {hn} {hl} {mn} {ml} {sn} {sl}',
      'late delivery notice': '<p>Din ordre indeholder vare(r) vi ikke har på lager i øjeblikket. Ordren pakkes når vi har alle varer på vores lager.</p><p>Hele din ordre forventes afsendt fra vores lager senest: <strong>%date%</strong>.',
      'delete from basket warning' : 'Er du sikker på du vil slette <strong>%product%</strong> fra din kurv ?',
      'late delivery' : '<h2>Bemærk</h2><strong>"%product%"</strong> har vi ikke på lager i øjeblikket.<br>Hele din ordre forventes afsendt fra vores lager senest <strong>%date%</strong>.',
      'Not filled correctly': 'Ikke udfyldt korrekt',
      'checkout.shipping_business' : 'Du har valgt "Post Danmark Erhverv". Husk at ændre leveringsadressen ved at klikke på "Ret leveringsadresse" (leveringsadressen skal ved denne fragttype være en erhvervsadresse).',
      'loading.std' : 'Indlæser ...',
      'loading.search' : 'Søger ...',
      'loading.search.products' : 'Finder produkter ...'
    },
    en : {
      'countdown date' : 'February 20, 2012 09:00:00',
      'countdown format' : '{d<}{dn} {dl} {d>} {hn} {hl} {mn} {ml} {sn} {sl}',
      'late delivery notice': '<p>Your order includes item(s) we do not have in stock at the moment. The order will be packed, when all items are back in stock. </p><p>Your order will be shipped from our warehouse at the latest: <strong>%date%</strong>.</p>',
      'delete from basket warning' : 'Are you sure you want to delete <strong>%product%</strong> from your cart?',
      'late delivery' : '<h2>Notice</h2><strong>"%product%"</strong> currently not in stock.<br>Your order will be shipped from our warehouse at the latest: <strong>%date%</strong>.',
      'loading.std' : 'Loading ...',
      'loading.search' : 'Searching ...',
      'loading.search.products' : 'Finding products ...'
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
