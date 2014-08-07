var gothia = (function($) {
  var pub = {};

  var confirmInit = function() {
      $("#gothia-payment-step-2").show();

      $("#gothia-confirm-container form").on('submit', function(event) {
        event.preventDefault();
        dialoug.loading( '#action-submit-gothia-confirm', Translator.trans('please.wait') );

        var data = $(this).serialize();
        var url  = $(this).attr('action');

        $.ajax({
          url:      url,
          type:     'post',
          dataType: 'json',
          data:     data,
          success:  function(data) {
            dialoug.stopLoading();
            if ( data.status === true ) {
              $("#gothia-payment-step-3 form").submit();
            } else {
              dialoug.error( Translator.trans('an.error.occurred'), data.message );
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.stopLoading();
            dialoug.error( Translator.trans('an.error.occurred'), errorThrown );
          }
        });
      });
  };

  var checkCustomerInit = function() {
    $("#gothia-payment-step-1").show();

    $("#gothia-account-container form").on('submit', function(event) {
      event.preventDefault();

      $form = $(this);
      $('.buttons', $form).hide();
      var data = $form.serialize();
      var url  = $form.attr('action');

      dialoug.loading( '#form_social_security_num', Translator.trans('please.wait') );
      //$("#gothia-account-container form input").attr('disabled', 'disabled');

      $.ajax({
        url:      url,
        type:     'post',
        dataType: 'json',
        data:     data,
        success:  function(data) {
          dialoug.stopLoading();

          if ( data.status === true ) {
            $("#gothia-payment-step-1").slideUp();
            $("#gothia-payment-step-2").slideDown();
            confirmInit();
            $("#gothia-payment-step-2 form").submit(); // TODO: Dette burde gøre så vi slap for et step!
          } else {
            //$("#gothia-account-container form input").removeAttr('disabled');
            $('.buttons', $form).show();
            dialoug.error( Translator.trans('an.error.occurred'), data.message );
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          //$("#gothia-account-container form input").removeAttr('disabled');
          $('.buttons', $form).show();
          dialoug.stopLoading();
          dialoug.error( Translator.trans('an.error.occurred'), errorThrown );
        }
      });
    });

    // If the SSN is already known, do the submit to trigger checkCustomer
    if ($('#form_social_security_num').val() && ($('#form_bank_account_no').length === 0)) {
      $("#gothia-account-container form").submit();
    }
  };

  pub.init = function(step) {
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
