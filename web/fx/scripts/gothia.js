var gothia = (function ($) {
    var pub = {};

    var confirmInit = function () {
        $("#gothia-payment-step-2").show();
        var $errorBlock = $('.form-error-block'),
            $submit = $('#action-submit-gothia-confirm');

        $("#form_bank_account_no").on('blur focusout', function(event) {
          var ibanRegEx = /DE\d{20}/i;

          if (!$errorBlock.hasClass('off')) {
            $errorBlock.addClass('off');
          }
          if (!ibanRegEx.test($(this).val())) {
            $submit.prop('disabled', true).hide();
            $errorBlock.removeClass('off').html(Translator.trans('checkout.payment.gothia.iban.error'));
          } else {
            $errorBlock.addClass('off');
            $submit.prop('disabled', false).show();
          }
        });

        $("#form_bank_id").on('blur focusout', function(event) {
          if (!$errorBlock.hasClass('off')) {
            $errorBlock.addClass('off');
          }
          if ($(this).val().length != 11) {
            $submit.prop('disabled', true).hide();
            $errorBlock.removeClass('off').html(Translator.trans('checkout.payment.gothia.bic.error'));
          } else {
            $errorBlock.addClass('off');
            $submit.prop('disabled', false).show();
          }
        });

        $("#gothia-confirm-container form").on('submit', function (event) {
            event.preventDefault();

            dialoug.blockingNotice(
                Translator.trans('please.wait'),
                Translator.trans('checkout.payment.progress.alert.message', {'url': base_url + 'payment/cancel'})
            );

            var data = $(this).serialize();
            var url  = $(this).attr('action');
            var $errorBlock = $('.form-error-block');

            if (!$errorBlock.hasClass('off')) {
                $errorBlock.addClass('off');
            }

            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (data) {
                    $.colorbox.close();
                    if (data.status === true) {
                        $("#gothia-payment-step-3 form").submit();
                    } else {
                        $.colorbox.close();
                        $errorBlock.removeClass('off').html(data.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $.colorbox.close();
                    $errorBlock.removeClass('off').html(errorThrown);
                }
            });
        });
    };

    var checkCustomerInit = function () {
        $("#gothia-payment-step-1").show();

        $("#gothia-account-container form").on('submit', function (event) {
            event.preventDefault();

            var $form = $(this);
            $('.buttons', $form).hide();

            var data = $form.serialize();
            var url  = $form.attr('action');

            dialoug.loading('#form_social_security_num', Translator.trans('please.wait'));

            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                data: data,
                success: function (data) {
                    dialoug.stopLoading();

                    if (data.status === true) {
                        $("#gothia-payment-step-1").slideUp();
                        $("#gothia-payment-step-2").slideDown();
                        confirmInit();
                        $("#gothia-payment-step-2 form").submit(); // TODO: Dette burde gøre så vi slap for et step!
                    } else {
                        $('.buttons', $form).show();
                        dialoug.error(Translator.trans('an.error.occurred'), data.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('.buttons', $form).show();
                    dialoug.stopLoading();
                    dialoug.error(Translator.trans('an.error.occurred'), errorThrown);
                }
            });
        });

        // If the SSN is already known, do the submit to trigger checkCustomer
        if ($('#form_social_security_num').val() && ($('#form_bank_account_no').length === 0)) {
            $("#gothia-account-container form").submit();
        }
    };

    pub.init = function (step) {
        switch (step) {
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
