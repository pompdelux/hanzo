var i18n = (function($) {
  var pub = {}

  // TODO: move to controller
  var map = {
    da : {
      'Close' : 'Luk',
      'Ok' : 'Ok',
      'Cancel' : 'Annuller',
      'notice' : 'Bemærk!',
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
      'loading.search.products' : 'Finder produkter ...',
      'edit order notice' : '<font color="#A10000"><b>Det er muligt at din ordres afsendelsesdato ændre sig til en senere afsendelsesdato ved redigering.</b></font><br><br>For at redigere din ordre skal du gøre følgende:<br><br>1) Klik på "Rediger" for at redigere ordren. Dine varer vil herefter blive lagt tilbage i kurven.<br><br>2) Ønsker du at ændre en farve eller en størrelse, skal du klikke på blyanten i kurven. Ønsker du at tilføje varer, skal du klikke på menuen Webshop.<br><br>3) Når du er færdig med dine ændringer, skal du klikke på "Vis kurv" og følge trin 1 til 5. Når du har gennemført din ordre med ny kreditkortbetaling, vil den gamle betaling automatisk blive slettet.<br><br><b>Bemærk:</b> Hvis du anvender Visa Electron eller MasterCard skal du være opmærksom på følgende:<ul><li>Der foretages endnu en reservation af det fulde købs beløb når du redigerer din ordre. </li><li>Reservationen på din oprindelige ordre bliver straks annulleret af POMPdeLUX og vi har således kun reserveret købs beløbet på din redigerede ordre. Hos dit pengeinstitut kan der imidlertid gå op til 6 uger inden reservationen ophæves. </li></ul>'
    },
    en : {
      'notice' : 'Notice!',
      'countdown date' : 'February 20, 2012 09:00:00',
      'countdown format' : '{d<}{dn} {dl} {d>} {hn} {hl} {mn} {ml} {sn} {sl}',
      'late delivery notice': '<p>Your order includes item(s) we do not have in stock at the moment. The order will be packed, when all items are back in stock. </p><p>Your order will be shipped from our warehouse at the latest: <strong>%date%</strong>.</p>',
      'delete from basket warning' : 'Are you sure you want to delete <strong>%product%</strong> from your cart?',
      'late delivery' : '<h2>Notice</h2><strong>"%product%"</strong> currently not in stock.<br>Your order will be shipped from our warehouse at the latest: <strong>%date%</strong>.',
      'loading.std' : 'Loading ...',
      'loading.search' : 'Searching ...',
      'loading.search.products' : 'Finding products ...',
      'edit order notice' : 'To edit your order you have to do the following:<br /><br />1) Click on "Edit" to edit the order. Your items will then be returned to the basket.<br /><br />2) If you wish to change colour or size, click on the pencil in the basket. If you wish to add items, click on the menu Webshop.<br /><br />3) When you have finished editing, click on "Show basket" and follow step 1 to 5. When you have completed your order with new credit card payment, the old payment will automatically be cancelled.'
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
