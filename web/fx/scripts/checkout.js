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
          attachLocationForm($('#address-block form.location-locator'));
        } else if (element.name == 'paytype') {
          checkout.setStepStatus('payment', true);
          $('#gift-card-block').removeClass('hidden');
          $('#coupon-block').removeClass('hidden');
        }
      });

      // TODO: move to central place and re-use.
      // zip with auto city
      $(document).on('focusout blur', 'input.auto-city', function(event) {
        var $this = $(this);
        var $form = $this.closest('form');

        // TODO: use css class
        $this.css('border', '1px solid #231F20');
        dialoug.loading($this);

        var value = $(this).prop('value');

        if ('' === value) {
          return;
        }

        $.getJSON(base_url+'muneris/gpc/'+value, function(response) {
          var $city = $('.js-auto-city-'+$form.data('addresstype'), $form),
              $city_dropdown = $city.parent().find('.js-auto-city-dropdown');
          if (response.status) {
            if (response.data.postcodes.length > 1) {
              // Many cities with same zip.
              // Hide the city field and add a dropdown with all the cities.
              $city.hide();
              if ($city_dropdown.length === 0) {
                $city_dropdown = $('<select class="js-auto-city-dropdown"></select>')
                  .appendTo($city.parent())
                  .on('change', function(e){
                    $city.val(this.value);
                  });
              } else {
                $('option', $city_dropdown).remove();
                $city_dropdown.show();
              }
              $.each(response.data.postcodes, function(index, postcode){
                // Add all cities as an option.
                $city_dropdown.append($('<option value="' + postcode.city + '">' + postcode.city + '</option>'));
              });
            } else {
              // Only 1 result.
              if ($city_dropdown) {
                $city_dropdown.hide();
              }
              $city.show();
            }
            $city.val(response.data.postcodes[0].city);
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
        var $checkout_button = $(this);
        /**
         * 1: validering af alle steps
         * 2: saml og POST alle forms og få deres svar retur
         */
        $('.msg.error').toggleClass('hidden error');

        // shipping
        if (!checkout.getStepStatus('shipping')) {
          var $block = $('#shipping-block');
          $('.msg', $block).text(Translator.get('js:checkout.choose.shipping.method')).toggleClass('hidden error');
          $('html,body').animate({ scrollTop : $('#shipping-block').offset().top - 20 });
          return false;
        }

        // payment
        if (!checkout.getStepStatus('payment')) {
          var $block = $('#payment-block');
          $('.msg', $block).text(Translator.get('js:checkout.choose.payment.method')).toggleClass('hidden error');
          $('html,body').animate({ scrollTop : $('#payment-block').offset().top - 20 });
          return false;
        }

        var address_errors = {
          has_errors : false,
          fields : []
        };

        // Be sure to copy the final address if "Copy address" is checked.
        copyAddress();

        var $address_confirm_box= $('<div></div>').addClass('address-confirm-box clearfix');

        $('#address-block form').not('.location-locator').each(function (index, form) {
          var $form = $(form);
          var id = index;

          var $address_ul = $('<ul></ul>');
          $('input, select', $form).not('.js-auto-city-dropdown').each(function (index, element) {
            var $element = $(element);
            // TODO: use css class
            $element.css({'border': '1px solid #231F20'});

            var field = element.name.match(/\[([a-z]{1}[a-z_0-9]+)\]/);

            if (field && (element.value === '')) {
              $element.css({'border': '2px solid #f00'});
              address_errors.has_errors = true;
              address_errors.fields.push(field);
            }
            // Add the element to the confirm address box
            if ($element.attr('type') !== 'hidden' && element.id !== 'form_phone') {
              $address_ul.append('<li><b>' + $('label[for=' + element.id + ']').first().text() + '</b> ' + element.value + '</li>');
            }
          });
          $address_confirm_box.append($('<div>').append($(this).parent().find('h3').clone())
                                                .append($address_ul));
        });

        if (address_errors.has_errors) {
          dialoug.notice(Translator.get('js:not.filled.correctly'), 'error', 4000, '#address-block');
          $('html,body').animate({ scrollTop : $('#address-block').offset().top - 20 });
          return false;
        }

        // Confirm that the entered addresses are correct.
        dialoug.confirm(Translator.get('js:checkout.confirm.address.block'), $address_confirm_box[0].outerHTML, function(choice) {
          if (choice !== 'ok') {
            var elementHeight = $('#address-block').height(),
                elementOffsetTop = $('#address-block').offset().top,
                viewportHeight = jQuery(window).height();
            // Scroll to address block centered in window.
            $('html,body').animate({ scrollTop : elementOffsetTop + (elementHeight/2) - (viewportHeight/2) });
          } else {
            executeCheckout();
          }
        }, {'maxWidth' : false});

        return false;
      });

      $(document).on('payment.method.updated', function(event) {
        $('.voucher-block').removeClass('hidden');
      });

      var $block = $('.voucher-block');
      $('a', $block).on('click', function(event) {
        event.preventDefault();
        $(this).next().toggle();
      });

      $('form', $block).on('submit', function(event) {
        event.preventDefault();

        var $msg = $('form .msg', $block);
        if (!$msg.hasClass('off')) {
          $msg.addClass('off');
        }

        var $form = $(this);
        dialoug.loading($('.button', $form));

        var callback = checkout.handleGiftCardUpdates;
        if ('apply-coupon' == $form.attr('id')) {
          callback = checkout.handleCouponUpdates;
        }

        var action = $form.attr('action').replace(/^\/[a-z]{2}_[A-Z]{2}/, '');

        jaiks.add(action, callback, {code: $('input#form_code', $form).val()});
        jaiks.add('/checkout/summery', checkout.handleSummeryUpdates);
        jaiks.exec();
      });

      $('#address-copy').on('change',function(e){
        var $copied = $('#address-block form:nth-child(2)');
        if (!copyAddress()) {
          // If address wasnt copied, reset the second.
          $copied.each(function(){
            this.reset();
          });
        }
      });

      if ($('.js-invalid-order').length) {
        $('#checkout-buttons').hide();
      }
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
        $('#address-block > div:nth-child(2)').replaceWith(response.response.data.html);
        $(document).trigger('shipping.address.changed');

        var m = $('input[name=method]:checked').val();
        if ((m === "10") || (m === "30") || (m === "70") || (m === "500") || (m === "601")) { // Private postal
          $('#address-copy').prop('checked', false).parent().removeClass('off');
        } else {
          $('#address-copy').parent().addClass('off');
        }

        attachLocationForm($('#address-block form.location-locator'));

        pub.setStepStatus('shipping', true);
      } else {
        pub.handleCallbackErrors(response);
      }
    };

    pub.handleLocationLocatorUpdates = function(response) {
      var $form = $('#address-block form.location-locator');
      $('table.locator-result, div.error', $form).remove();

      if (response.response.status) {
        if ($('.locator-result', $form).length) {
          $('.locator-result', $form).remove();
        }
        $form.append(response.response.data.html);

        $('input.droppoint-locator', $form).on('change', function(event) {
          var $this = $(this);
          var data = $this.data('entry');
          var $address = $form.next('form');

          $('input#form_company_name', $address).val(data.name);
          $('input#form_address_line_1', $address).val(data.address);
          $('input#form_postal_code', $address).val(data.postal_code);
          $('input#form_city', $address).val(data.city);
          $('input#form_external_address_id', $address).val(data.id);
        });
      } else {
        $form.append('<div class="error"><p>'+response.response.message+'</p></div>');
      }
      dialoug.stopLoading();
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
        $(document).trigger('payment.method.updated');
        pub.setStepStatus('payment', true);
      } else {
        pub.handleCallbackErrors(response);
      }

      pub.handleSummeryUpdates(response);
    };

    pub.handleGiftCardUpdates = function(response) {
      if (stop) { return; }

      var $gift_card = $('#gift-card-block');
      if (response.response.status) {
        $('form', $gift_card).hide();
      } else {
        $('form .msg', $gift_card).text(response.response.message).toggleClass('off hidden');
      }
    };

    pub.handleCouponUpdates = function(response) {
      if (stop) { return; }

      var $coupon = $('#coupon-block');
      if (response.response.status) {
        $('form', $coupon).hide();
      } else {
        $('form .msg', $coupon).text(response.response.message).toggleClass('off hidden');
      }
    };

    pub.validateAddress = function(response) {
      if (stop) { return; }

      if (true === pub.getStepStatus('address')) {
        pub.setStepStatus('address', response.response.status);
      }

      if (!pub.getStepStatus('address') && response.response.message) {
        var $form = $("form[action$='"+response.action+"']");
        $('ul.error', $form).remove();
        $form.prepend(response.response.message);
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
          if ('http' == response.response.data.url.substring(0, 4)) {
            document.location.href = response.response.data.url;
          } else {
            if ('/' == response.response.data.url.substring(0, 1)) {
              response.response.data.url = response.response.data.url.substring(1);
            }
            document.location.href = base_url+response.response.data.url;
          }
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
      } else {
        dialoug.stopLoading();
        $('a#checkout-execute').show();
        // reset address validation
        pub.setStepStatus('address', true);
      }
    };

    pub.handleCallbackErrors = function(response) {
      if ((false === response.response.status) && response.response.message) {
        stop = true;
        dialoug.stopLoading();
        dialoug.notice(response.response.message, 'error');
      }
    };

    attachLocationForm = function($form) {
      $form.on('submit', function(event) {

        if ($('.locator-result', $form).length) {
          $('.locator-result', $form).remove();
        }

        event.preventDefault();
        dialoug.loading($('.button', $(this)));
        jaiks.add('/location/locator', checkout.handleLocationLocatorUpdates, $form.formParams());
        jaiks.exec();
      });
    };

    executeCheckout = function() {

        $('a#checkout-execute').hide();
        dialoug.loading($('a#checkout-execute', '', 'after'));
        $('form.address .error').remove();

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

      };

    /**
     * Copies the address from first address block to second.
     * @return boolean True if address was copied.
     */
    copyAddress = function() {
      if ($('#address-copy').prop('checked')) {
        var $copied = $('#address-block form:nth-child(2)');
        $('#address-block form:first input[type=text]').each(function(i){
          $copied.find('#'+$(this).attr('id')).val($(this).val());
        });
        return true;
      }
      return false;
    };

    return pub;
  })(jQuery);

  if ($('#body-checkout').length) {
    checkout.init();
  }

})(jQuery);
