var gothia = (function($) {
  var pub = {};

  pub.init = function( step ) {
        switch(step) {
          case 1:
            checkCustomerInit();
            break;
          case 2:
            confirmInit();
            break;
        }
  };

  checkCustomerInit = function() {
      $("#gothia-payment-step-1").show();

      $("#gothia-account-container form").on('submit', function(event) {
        event.preventDefault();
        // FIXME: text
        dialoug.loading( 'Vent venligst' );
        var data = $(this).serialize();
        var url = $(this).attr('action');
        $.ajax({
          url: url,
          type: 'post',
          dataType: 'json',
          data: data,
          success: function(data) {
            dialoug.stopLoading();

            if ( data === 'ok') { /* FIXME */
              $("#gothia-account-container form input").attr('disabled', 'disabled');
              $("#gothia-payment-step-2").slideDown();
              confirmInit();
            }
            else
            {
              // FIXME:
              dialoug.error( data.message );
            }
          }
        });
      });
  };

  confirmInit = function() {
      $("#gothia-payment-step-2").show();
    
      $("#gothia-confirm-container form").on('submit', function(event) {
        event.preventDefault();
        // FIXME: text
        dialoug.loading( 'Vent venligst' );
        var data = $(this).serialize();
        var url = $(this).attr('action');
        $.ajax({
          url: url,
          type: 'post',
          dataType: 'json',
          data: data,
          success: function(data) {
            dialoug.stopLoading();
            if ( data === 'ok') { /* FIXME */
              $("#gothia-payment-step-3 form").submit();
            }
          }
        });
      });
  };

  return pub;
})(jQuery);
