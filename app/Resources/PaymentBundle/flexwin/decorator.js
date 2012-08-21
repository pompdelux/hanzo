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
    $("#auth").submit(function(){
        if ( $('#auth input.error').length )
        {
          return;
        }
        $("#btnAuthSubmit").css(cssObj);
      });
});
