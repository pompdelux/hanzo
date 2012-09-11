window.onbeforeunload = function (e) {
  var message = 'Du har klikket på browserens tilbageknap. Ønsker du at afbryde betalingen, skal du klikke på "Bliv på siden" og herefter klikke på knappen "Afbryd"',
  e = e || window.event;

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
      console.log('Cancel auth');
      if ( $('#auth input.error').length ) {
        return;
      }
      window.onbeforeunload = function (e) {};
      $("#btnAuthSubmit").css(cssObj);
    });
    $("#cancel").submit(function() {
      console.log('Cancel submit');
      window.onbeforeunload = function (e) {};
    });
});
