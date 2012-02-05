var gothia = (function($) {
  var pub = {};

  var confirmInit = function() {
      $("#gothia-payment-step-2").show();

      $("#gothia-confirm-container form").on('submit', function(event) {
        event.preventDefault();
        dialoug.loading( '#form_social_security_num', i18n.t('Please wait') );
        var data = $(this).serialize();
        var url = $(this).attr('action');
        $.ajax({
          url: url,
          type: 'post',
          dataType: 'json',
          data: data,
          success: function(data) {
            dialoug.stopLoading();
            if ( data.status === true ) {
              $("#gothia-payment-step-3 form").submit();
            }
            else
            {
              // FIXME: text
              dialoug.error( 'Der opstod en fejl', data.message );
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.stopLoading();
              // FIXME: text
              dialoug.error( 'Der opstod en fejl', errorThrown );
          }
        });
      });
  };

  var checkCustomerInit = function() {
    $("#gothia-payment-step-1").show();

    $("#gothia-account-container form").on('submit', function(event) {
      event.preventDefault();

      var data = $(this).serialize();
      var url = $(this).attr('action');

      dialoug.loading( '#form_social_security_num', i18n.t('Please wait') );
      $("#gothia-account-container form input").attr('disabled', 'disabled');

      $.ajax({
        url: url,
        type: 'post',
        dataType: 'json',
        data: data,
        success: function(data) {
          dialoug.stopLoading();

          if ( data.status === true ) {
            $("#gothia-payment-step-2").slideDown();
            confirmInit();
          }
          else
          {
            $("#gothia-account-container form input").removeAttr('disabled');
            // FIXME: text
            dialoug.error( 'Der opstod en fejl', data.message );
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          $("#gothia-account-container form input").removeAttr('disabled');
          dialoug.stopLoading();
          // FIXME: text
          dialoug.error( 'Der opstod en fejl', errorThrown );
        }
      });
    });
  };

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

  return pub;
})(jQuery);
