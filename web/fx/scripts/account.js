var account = (function($) {
  var pub = {};

  pub.init = function() {
    nnoInit();
    zipToCityInit();

    if($('form.create').length) {
      var $form = $('form.create');
      var $a = $('form.create a');
      $form.find('label[for="customers_accept"]').append($a);
    }

    $('a[rel="colorbox"]').colorbox();
  };

  function nnoInit()
  {
    $('form.nno').on('submit', function(event) {
      event.preventDefault();
      var $form = $(this);
      var $input = $('input[name="phone"]', $form);
      if ($input.val()) {
        // trigger loading box
        dialoug.loading('form.nno input[type="submit"]', 'søger efter adresse...');
        // fetch data
        $.getJSON(this.action + '/' + $input.val() , function(result) {
          if (result.status) {
            var $target = $('form.create');
            $target.find('#customers_first_name').val(result.data.christianname);
            $target.find('#customers_last_name').val(result.data.surname);
            $target.find('#customers_addresses_0_address_line_1').val(result.data.address);
            $target.find('#customers_addresses_0_postal_code').val(result.data.zipcode);
            $target.find('#customers_addresses_0_city').val(result.data.district);
            $target.find('#customers_phone').val(result.data.phone);
          }
          else {
            dialoug.alert('Woops!', result.message);
          }
          // reset form and kill loader
          $input.val('');
          dialoug.stopLoading();
        });
      }

      return false;
    });
  }

  function zipToCityInit()
  {
    /**
     * auto complete city names when entering zip codes.
     * only works for se/no/dk so if the request is made via .com we skip this step
     */
    var tld = document.location.hostname.match(new RegExp("\.([a-z,A-Z]{2,6})$"));
    try {
      if (tld[1] != 'com' && tld[1] != 'nl') {
        $('#customers_addresses_0_city').attr('readonly', 'readonly');

        // set city to readonly
        $('#customers_addresses_0_city').focus(function () {
          dialoug.loading( '#customers_addresses_0_city', i18n.t('Please wait') );
          this.value = '';
          if ($('#customers_addresses_0_postal_code').val() == '') {
            $('#customers_addresses_0_postal_code')
            .css('border-color', '#a10000')
            .fadeOut(100).fadeIn(100)
            .fadeOut(100).fadeIn(100)
            .fadeOut(100).fadeIn(100)
            .focus();
            dialoug.stopLoading();
            return;
          }
          $.getJSON( base_url+'service/get-city-from-zip/'+$('#customers_addresses_0_postal_code').val(), function(data) {
            if (data.status && data.data.city) {
              $('#customers_addresses_0_city').val(data.data.city);
              $('#customers_addresses_0_postal_code').css('border-color', '#444345');
              try {
                $('#customers_phone').focus();
              } catch (e) {}
            }
            else {
              $('#customers_addresses_0_postal_code').css('border-color', '#a10000').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).focus();
            }
            dialoug.stopLoading();
          });
        });
      }
    } catch (e) {}
  }

  return pub;
})(jQuery);

if ($("#body-create-account").length) {
  account.init();
}
