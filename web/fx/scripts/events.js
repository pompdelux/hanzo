var calendar = (function($) {
  var pub = {};


  pub.init = function() {

  };



  return pub;
})(jQuery);



if ($("#calendar").length) {
  calendar.init();
}

$('#find-customer-by-phone-form').submit(function(e){
	e.preventDefault();
	$submit = $(this).find('input[type=submit]');
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
        	console.log(response.data);
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
	$submit = $(this).find('input[type=submit]');
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
        	console.log(response.data);
        	$('#form_customers_id').val(response.data.id);
        	$('#form_host').val(response.data.name);
        	$('#form_phone').val(response.data.phone);
        	$('#form_email').val(response.data.email);
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
	$a = $(this);
	dialoug.confirm( ExposeTranslation.get('js:notice'), $(this).data('confirm-message'), function(choise) {
	    if (choise === 'ok') {
	    	window.location.href = $a.attr('href');
	    }
	});
});
