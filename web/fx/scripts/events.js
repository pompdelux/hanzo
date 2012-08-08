var calendar = (function($) {
  var pub = {};

  pub.init = function() {
      $("a.open-menu").click(function(e) {
        e.preventDefault();
        $(this).parent().find('div').slideToggle();
      });
  };

  return pub;
})(jQuery);

// Used by events create customer
var events = (function($) {
  var pub = {};

  pub.create_customer_init = function() {
      fetch_customer();
      customers_accept();
  };

  var fetch_customer = function() {
    $("#fetch-customer-form").submit(function(e) {
      e.preventDefault();

      var url = base_url;
      var data = {
        value: $("#fetch-customer-form input").val()
      };

      var reg = new RegExp("^[0-9]+$");

      if ( reg.test(data.value) ) {
          url += 'events/fetch-customer/phone/';
      }
      else {
          url += 'events/fetch-customer/email/';
      }

      $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function(data) {
          // do stuff 
        }
      });
    });
  };

  var customers_accept = function() {
      if ($('#customers_accept:checked').length) {
          $('form.create input.button').show();
      }
      $('#customers_accept').on('change', function() {
          if ($(this).prop("checked")) {
              $('form.create input.button').show();
          } else {
              $('form.create input.button').hide();
          }
      });
  };

  return pub;
})(jQuery);

if ($("#calendar").length) {
  calendar.init();
}

$('#body-event #participants a.delete').on('click', function(event) {
  event.preventDefault();
  var $this = $(this);
  var tr = $this.closest('tr');

  dialoug.confirm( ExposeTranslation.get('js:notice'), ExposeTranslation.get('js:event.confirm.delete.participant')+'<br><strong>'+tr.find('td.name').text()+'</strong>', function(choise) {
    if (choise === 'ok') {
      $.post(base_url+'events/remove/participant/'+$this.data('event')+'/'+$this.data('participant'), function(response) {
        tr.fadeOut();
      });
    }
  });
});

$('#find-customer-by-phone-form').submit(function(e){
  e.preventDefault();
  var $submit = $(this).find('input[type="submit"]');
  $submit.attr('disabled', true);
  var phone = $('#find-customer-by-phone').val();
  $.ajax({
      url: base_url + 'account/nno/' + phone,
      dataType: 'json',
      async: false,
      success: function(response, textStatus, jqXHR) {
        if (false === response.status) {
          if (response.message) {
            dialoug.alert(ExposeTranslation.get('js:notice'), response.message);
          }
        } else {
          $('#form_host').val(response.data.christianname + ' ' + response.data.surname);
          $('#form_address_line_1').val(response.data.address);
          $('#form_postal_code').val(response.data.zipcode);
          $('#form_city').val(response.data.district);
          $('#form_phone').val(response.data.phone);
        }

        $submit.attr('disabled', false);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        dialoug.error(ExposeTranslation.get('js:notice'), ExposeTranslation.get('js:an.error.occurred'));
        $submit.attr('disabled', false);
      }
    });
});

$('#find-customer-by-email-form').submit(function(e){
  e.preventDefault();
  var $submit = $(this).find('input[type="submit"]');
  $submit.attr('disabled', true);
  var email = $('#find-customer-by-email').val();
  $.ajax({
      url: base_url + 'events/getcustomer/' + encodeURIComponent(email),
      dataType: 'json',
      async: false,
      success: function(response, textStatus, jqXHR) {
        if (false === response.status) {
          if (response.message) {
            dialoug.alert(ExposeTranslation.get('js:notice'), response.message);
          }
        } else {
          $('#form_customers_id').val(response.data.id);
          $('#form_host').val(response.data.name);
          $('#form_phone').val(response.data.phone);
          $('#form_email').val(response.data.email);
          $('#form_address_line_1').val(response.data.address);
          $('#form_postal_code').val(response.data.zip);
          $('#form_city').val(response.data.city);
        }

        $submit.attr('disabled', false);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        dialoug.error(ExposeTranslation.get('js:notice'), ExposeTranslation.get('js:an.error.occurred'));
        $submit.attr('disabled', false);
      }
    });
});

$('.delete-event').click(function(e){
  e.preventDefault();
  var $a = $(this);
  dialoug.confirm( ExposeTranslation.get('js:notice'), $(this).data('confirm-message'), function(choise) {
    if (choise === 'ok') {
      window.location.href = $a.attr('href');
    }
  });
});
