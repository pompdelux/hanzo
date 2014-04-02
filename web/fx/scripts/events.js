/*global base_url:true, newsletter:true*/
var calendar = (function($) {
  var pub = {};

  pub.init = function() {};

  return pub;
})(jQuery);

// Used by events create customer
var events = (function($) {
  var pub = {};
  pub.init = function(){
    $('form.invite-form').submit(function(e){
      // regex source: http://stackoverflow.com/questions/46155/validate-email-address-in-javascript
      var email_regex = RegExp(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
      if(!$('#form_email', $(this)).val() && !$('#form_phone', $(this)).val()){
        e.preventDefault();
        dialoug.notice(Translator.get('js:events.error.email.or.phone'), 'error',3000, $(this));
      }
      if($('#form_email', $(this)).val() && !email_regex.test($('#form_email', $(this)).val())){
        e.preventDefault();
        dialoug.notice(Translator.get('js:email.invalid'), 'error',3000, $(this));
      }
    });
  };
  pub.choose_evet_type_init = function() {
    var $select = $('select#sales-type');
    var $hostess = $select.next();

    if ('AR' ==  $('option:selected', $select).data('type')) {
      $hostess.show();
    }

    $select.on('change', function() {
      $hostess.hide();
      if ('AR' ==  $('option:selected', $select).data('type')) {
        $hostess.show();
      }
    });
  };

  pub.create_customer_init = function() {
      fetch_customer();
      customers_accept();
  };

  var fetch_customer = function() {
    $("#fetch-customer-form").submit(function(event) {
      event.preventDefault();

      var $form = $(this);
      var value = $("input", $form).val();

      if (!value) { return; }
      dialoug.loading($('.button', $form));

      var url = base_url+'events/fetch-customer';
      var data = {
        value: value
      };

      $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function(response) {
          if (response.status) {
            var $c_form = $('form.create');
            $.each(response.data, function(key, value) {
              if ((key == 'newsletter') && value) {
                $('#customers_newsletter').prop('checked', 'checked');
                return;
              }
              $('input[id$="'+key+'"]', $c_form).val(value);
            });

            if (undefined !== response.data.id) {
              $('#customers_email_email_address_repeated', $c_form).val($('#customers_email_email_address', $c_form).val());
              $('#customers_password_pass', $c_form).parent().remove();
              $('#customers_password_pass_repeated', $c_form).parent().remove();
              $('#customers_id', $c_form).val(response.data.id);
              $('.input', $form).val('');
            }

            if (undefined === response.data.first_name) {
              dialoug.alert(Translator.get('js:notice'), Translator.get('js:event.user.email.not.found'));
            }
          } else {
            dialoug.alert(Translator.get('js:notice'), Translator.get('js:event.user.email.not.found'));
          }

          dialoug.stopLoading();
        },
        error: function() {
          dialoug.stopLoading();
        }
      });
    });
  };

  var customers_accept = function() {
      if ($('#customers_accept:checked').length) {
          $('form.create button[type=submit]').show();
      }
      $('#customers_accept').on('change', function() {
          if ($(this).prop("checked")) {
              $('form.create button[type=submit]').show();
          } else {
              $('form.create button[type=submit]').hide();
          }
      });
  };

  return pub;
})(jQuery);
events.init();
if ($("#calendar").length) {
  calendar.init();
}
if ($('#body-basket select#sales-type').length) {
  events.choose_evet_type_init();
}

$('#select-archived-events .open-menu').on('click', function(event){
  event.preventDefault();
  $(this).next().slideToggle();
});


$('#body-event .participants a.delete').on('click', function(event) {
  event.preventDefault();
  var $this = $(this);
  var tr = $this.closest('tr');

  dialoug.confirm( Translator.get('js:notice'), Translator.get('js:event.confirm.delete.participant')+'<br><strong>'+tr.find('td.name').text()+'</strong>', function(choise) {
    if (choise === 'ok') {
      $.post(base_url+'events/remove/participant/'+$this.data('event')+'/'+$this.data('participant'), function(response) {
        tr.fadeOut();
      });
    }
  });
});

$('#event-edit-form').submit(function(e){
  // IE fails to validate datetime fields, IDK why?
  if ($(this).find('#form_event_date').val() === '') {
    $(this).find('#form_event_date').focus();
    e.preventDefault();
  }
});

$('#find-customer-by-phone-form').submit(function(e){
  e.preventDefault();
  var $submit = $(this).find('input[type="submit"]');
  $submit.attr('disabled', true);
  var phone = $('#find-customer-by-phone').val();
  $.ajax({
      url: base_url + 'muneris/nno/' + phone,
      dataType: 'json',
      async: false,
      success: function(response, textStatus, jqXHR) {
        if (false === response.status) {
          if (response.message) {
            dialoug.alert(Translator.get('js:notice'), response.message);
          }
        } else {
          var data = response.data.number;
          $('#form_host').val(data.christianname + ' ' + data.surname);
          $('#form_address_line_1').val(data.address);
          $('#form_postal_code').val(data.zipcode);
          $('#form_city').val(data.district);
          $('#form_phone').val(data.phone);
        }

        $submit.attr('disabled', false);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
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
            dialoug.alert(Translator.get('js:notice'), response.message);
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
        dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
        $submit.attr('disabled', false);
      }
    });
});

$('.delete-event').click(function(e){
  e.preventDefault();
  var $a = $(this);
  dialoug.confirm( Translator.get('js:notice'), $(this).data('confirm-message'), function(choise) {
    if (choise === 'ok') {
      window.location.href = $a.attr('href');
    }
  });
});
