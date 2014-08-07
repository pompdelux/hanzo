var dibs_process = (function($) {
  var pub = {};
  var intervalID;
  var currentTimeSpend = 0;
  var maxttl = 60000;
  var interval = 5000;

  function checkState() {
      if ( currentTimeSpend >= maxttl ) {
          window.location = base_url+'checkout/failed';
      }

      currentTimeSpend += interval;

      $.ajax({
        url: base_url+'payment/dibs/state-check',
        type: 'post',
        dataType: 'json',
        async: false,
        cache: false,
        success: function(data) {
            if ( data.redirect_to_basket === true ) {
              window.location = base_url+'basket';
            }
            if ( parseInt( data.state,10 ) >= 20 ) {
              window.location = base_url+'checkout/success';
            }
        }
      });
  }

  pub.init = function() {
    intervalID = setInterval(checkState, interval);
    dialoug.loading($(".processing-text"), Translator.trans('please.wait'));
  };

  return pub;
})(jQuery);
