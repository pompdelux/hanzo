window.onbeforeunload = function (e) {
  var message = 'Du har klikket på browserens tilbageknap. Ønsker du at afbryde betalingen, skal du klikke på "Bliv på siden" og herefter klikke på knappen "Afbryd"',
  e = e || window.event;

  var lang = $('#cancelform_lang').val();
  switch (lang)
  {
    case 'en':
      message = 'You have clicked on the return button on the browser. If you wish to cancel/discontinue the payment, click on "stay on the site/page" and then click on "cancel/discontinue"';
    break;
    case 'no':
      message = 'Du har klikket på tilbakeknappen på browseren. Hvis du ønsker å avbryte betalingen, skal du klikke på "Bli på siden" og heretter klikke på knappen "Avbryt"';
    break;
    case 'sv':
      message = 'You have clicked on the return button on the browser. If you wish to cancel/discontinue the payment, click on "stay on the site/page" and then click on "cancel/discontinue"';
    break;
    case 'nl':
      message = 'Je hebt de gedrukt op de terug-toets van de browser. Wil je de betaling afbreken, klik dan op "Blijf op deze pagina" en klik hierna op de "Afbreken"';
    break;
    case 'fi':
      message = 'Olet klikannut selaimesi paluu nappia. Toivotko keskeyttäväsi maksun, täytyy sinun klikata "Pysy sivulla" ja tämän jälkeen klikata nappia "keskeytä"';
    break;
  }

  // For IE and Firefox
  if (e) {
    e.returnValue = message;
  }

  // For Safari
  return message;
};

$(document).ready(function(){
    var lang = $('#cancelform_lang').val();
    var buttonText = 'Udfør betaling';
    switch (lang)
    {
      case 'en':
        buttonText = 'Validate payment';
      break;
      case 'no':
        buttonText = 'Bekreft betaling';
      break;
      case 'sv':
        buttonText = 'Bekräfta betalning';
      break;
      case 'nl':
        buttonText = 'Uit te voeren betalingen';
      break;
      case 'fi':
        buttonText = 'Validate payment';
      break;
    }
    var cssObj = {
    'position' : 'absolute',
    'top' : '-9999px',
    'left' : '-9999px'
    };
    $('#btnAuthSubmit').val(buttonText);
    $('#btnAuthSubmit').text(buttonText);
    $("#btnAuthSubmit").css('display','inline');
    $("#auth").submit(function() {
      if ( $('#auth input.error').length ) {
        return;
      }
      $("#btnAuthSubmit").css(cssObj);
    });

    $("#btnAuthCancel").click(function(e) {
      e.preventDefault();
      window.onbeforeunload = function(e) {};
      $("#cancel").submit();
    });

    $("#btnAuthSubmit").click(function(e) {
      e.preventDefault();
      window.onbeforeunload = function(e) {};
      $("#auth").submit();
    });
});
