(function($, undefined){
  var checkout = (function($, undefined) {
    var pub = {};

    var step_states = {
      shipping: false,
      address: true,
      payment: false
    };

    var stop = false;

    pub.init = function() {

      $('#body-checkout form input:checked').each(function (index, element) {
        if (element.name == 'method') {
          checkout.setStepStatus('shipping', true);
        } else if (element.name == 'paytype') {
          checkout.setStepStatus('payment', true);
          $('#coupon-block').removeClass('hidden');
        }
      });

      // TODO: move to central place and re-use
      // zip with auto city
      $(document).on('focusout', 'input.auto-city', function(event) {
        var $this = $(this);
        // TODO: use css class
        $this.css('border', '1px solid #231F20');
        dialoug.loading($this);

        var value = $(this).prop('value');
        if ('' === value) {
          return;
        }

        $.getJSON(base_url+'service/get-city-from-zip/'+value, function(response) {
          var $city = $this.closest('div#form').find('input#form_city').first();

          if (response.status) {
            $city.val(response.data.city);
          } else {
            // TODO: use css class
            $this.css('border', '2px solid #a10000');
            $this.val('');
            $city.val('');
            $this.focus();
          }

          dialoug.stopLoading();
        });
      });

      $('#shipping-block input').on('change', function(event) {
        $(this).blur();
        $(document).trigger('shipping.method.changed', this);
      });

      $('#payment-block input').on('change', function(event) {
        $(this).blur();
        $(document).trigger('payment.method.changed', this);
      });

      // catch shipping method changes
      $(document).on('shipping.method.changed', function(event, element) {
        /**
         * 1: opdater ordre med leveringsmetode
         * 2: få adresseformen opdateret
         * 3: få ordretotalen opdateret
         * 4: overdrag til næste step (betaling)
         */
        dialoug.loading($('#shipping-block').next('h2'), '', 'append');
        $('.msg.error').toggleClass('hidden error');

        var $form = $(element).closest('form');
        var method = 'shipping';

        switch (element.value) {
          case '11':
            method = 'company_shipping';
            break;
          case '12':
            method = 'overnightbox';
            break;
        }

        jaiks.add('/checkout/shipping/set/method', pub.handleCallbackErrors, {method : element.value});
        jaiks.add('/checkout/shipping/address/'+method+'/form', checkout.handleShippingMethodUpdates);
        jaiks.add('/checkout/summery', checkout.handleSummeryUpdates);
        jaiks.exec();
      });


      $(document).on('payment.method.changed', function(event, element) {
        /**
         * 1: opdater ordre med betalingsmetode
         * 2: få ordretotalen opdateret
         * 3: overdrag til nsæte step (validering)
         */
        dialoug.loading($('#payment-block').prev('h2'), '', 'append');
        $('.msg.error').toggleClass('hidden error');

        var $ul = $(element).closest('ul');

        jaiks.add('/checkout/payment/set/method', pub.handleCallbackErrors, {method : element.value});
        jaiks.add('/checkout/summery', checkout.handlePaymentMethodUpdates);
        jaiks.exec();
      });


      $(document).on('click', 'a#checkout-execute', function(event) {
        event.preventDefault();
        /**
         * 1: validering af alle steps
         * 2: saml og POST alle forms og få deres svar retur
         */
        $('.msg.error').toggleClass('hidden error');

        // shipping
        if (!checkout.getStepStatus('shipping')) {
          var $block = $('#shipping-block');
          $('.msg', $block).text(Translator.get('js:checkout.choose.shipping.method')).toggleClass('hidden error');
          $('html,body').animate({ scrollTop : $block.prev('h2').offset().top - 20 });
          return false;
        }

        // payment
        if (!checkout.getStepStatus('payment')) {
          var $block = $('#payment-block');
          $('.msg', $block).text(Translator.get('js:checkout.choose.payment.method')).toggleClass('hidden error');
          $('html,body').animate({ scrollTop : $block.prev('h2').offset().top - 20 });
          return false;
        }

        var address_errors = {
          has_errors : false,
          fields : []
        };

        $('#address-block form').each(function (index, form) {
          var $form = $(form);
          var id = index;
          $('input, select', $form).each(function (index, element) {
            var $element = $(element);
            // TODO: use css class
            $element.css({'border': '1px solid #231F20'});

            var field = element.name.match(/\[([a-z]{1}[a-z_0-9]+)\]/);

            if (field && (element.value === '')) {
              $element.css({'border': '2px solid #f00'});
              address_errors.has_errors = true;
              address_errors.fields.push(field);
            }
          });
        });

        if (address_errors.has_errors) {
          dialoug.notice(Translator.get('js:not.filled.correctly'), 'error', 4000, '#address-block');
          $('html,body').animate({scrollTop: $('#address-block').prev('h2').offset().top});
          return false;
        }

        var address_confirm = $('#address-block div.confirm');

        if ($('input::checked', address_confirm).length === 0) {
          address_confirm.toggleClass('hidden');

          var t = document.getElementById('address-block').offsetTop;
          $('html,body').animate({scrollTop: t});
          return false;
        }

        dialoug.loading($('a#checkout-execute', '', 'before'));

        $('#main form').each(function(index, form) {
          var $form = $(form);

          if ($form.data('callback')) {
            var url = $form.attr('action');
            url = url.replace(/(app_(test|dev)\.php\/)?[a-z]{2}_[a-z]{2}\//i, '');
            var callback = $form.data('callback');
            jaiks.add(url, eval('checkout.'+callback), $form.formParams());
          }
        });

        jaiks.add('/checkout/payment/process', checkout.processPaymentButton);
        jaiks.exec();

        return false;
      });

      $('#addresses-confirmed').on('change', function(event) {
        if (this.checked) {
          $(this).closest('div').css('border-color', '#C8C4C3');
          var t = document.getElementById('checkout-buttons').offsetTop;
          $('html,body').animate({ scrollTop : t });
        }
      });

      $(document).on('payment.method.updated', function(event) {
        $('#coupon-block').removeClass('hidden');
      });

      var $coupon = $('#coupon-block');
      $('a', $coupon).on('click', function(event) {
        event.preventDefault();
        $(this).next().toggle();
      });

      $('form', $coupon).on('submit', function(event) {
        event.preventDefault();

        var $msg = $('form .msg', $coupon);
        if (!$msg.hasClass('off')) {
          $msg.addClass('off');
        }

        var $form = $(this);
        dialoug.loading($('.button', $form));

        jaiks.add('/checkout/coupon/apply', checkout.handleCouponUpdates, {code: $('input#form_code', $form).val()});
        jaiks.add('/checkout/summery', checkout.handleSummeryUpdates);
        jaiks.exec();
      });

      $('#address-copy').on('change',function(e){
        $copied = $('#address-block form:nth-child(2)');
        if($(this).attr('checked')){
          $('#address-block form:first input[type=text]').each(function(i){
            $copied.find('#'+$(this).attr('id')).val($(this).val());
          });
        }else{
          $copied.each(function(){
            this.reset();
          });
        }
      });
    };

    pub.setStepStatus = function(step, status) {
      step_states[step] = status;
    };

    pub.getStepStatus = function(step) {
      return step_states[step];
    };

    pub.handleShippingMethodUpdates = function(response) {
      if (stop) { return; }

      if (response.response.status) {
        $('#address-block form:nth-child(2)').replaceWith(response.response.data.html);
        $(document).trigger('shipping.address.changed');
        if($('input[name=method]:checked').val() === "10"){ // Private postal
          $('#address-copy').prop('checked', false).parent().removeClass('off');
        }else{
          $('#address-copy').parent().addClass('off');
        }
        $('html,body').animate({scrollTop: $('#address-block').prev('h2').offset().top - 20});
        pub.setStepStatus('shipping', true);
      } else {
        pub.handleCallbackErrors(response);
      }
    };

    pub.handleSummeryUpdates = function(response) {
      if (stop) { return; }

      if (response.response.status) {
        $('#checkout-block-summery').html(response.response.data);
        $(document).trigger('checkout.summery.updated');
      } else {
        pub.handleCallbackErrors(response);
      }

      dialoug.stopLoading();
    };

    pub.handlePaymentMethodUpdates = function(response) {
      if (stop) { return; }

      if (response.response.status) {
        var t = document.getElementById('checkout-block-summery').offsetTop;
        $('html,body').animate({scrollTop: t});
        $(document).trigger('payment.method.updated');
        pub.setStepStatus('payment', true);
      } else {
        pub.handleCallbackErrors(response);
      }

      pub.handleSummeryUpdates(response);
    };

    pub.handleCouponUpdates = function(response) {
      if (stop) { return; }

      var $coupon = $('#coupon-block');
      if (response.response.status) {
        $('form', $coupon).hide();
      } else {
        $('form .msg', $coupon).text(response.response.message).toggleClass('off');
      }
    };

    pub.validateAddress = function(response) {
      if (stop) { return; }

      if (true === pub.getStepStatus('address')) {
        pub.setStepStatus('address', response.response.status);
      }
    };

    pub.processPaymentButton = function(response) {
      if (stop) { return; }

      if (pub.getStepStatus('address') &&
          pub.getStepStatus('payment') &&
          pub.getStepStatus('shipping')
      ) {
        // payment-process-form
        if (undefined !== response.response.data.url) {
          if ('/' == response.response.data.url.substring(0, 1)) {
            response.response.data.url = response.response.data.url.substring(1);
          }
          document.location.href = base_url+response.response.data.url;
        } else if (undefined !== response.response.data.form) {
          $('#checkout-buttons').append(response.response.data.form);
          $('#checkout-buttons form').submit();
        }

        // pop "please be patient" notice
        window.setTimeout(function() {
          dialoug.blockingNotice(
            Translator.get('js:checkout.payment.progress.alert.title'),
            Translator.get('js:checkout.payment.progress.alert.message', {'url' : base_url+'payment/cancel'})
          );
        }, 3000);
      }
    };

    pub.handleCallbackErrors = function(response) {
      if ((false === response.response.status) && response.response.message) {
        stop = true;
        dialoug.stopLoading();
        dialoug.notice(response.response.message, 'error');
        $(document).scrollTop(50);
      }
    };

    return pub;
  })(jQuery);

  if ($('#body-checkout').length) {
    checkout.init();
  }

})(jQuery);
