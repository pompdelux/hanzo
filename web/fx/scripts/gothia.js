$(document).ready(function(){

  $("#gothia_payment_form").submit(function(e) {
    e.preventDefault();
    var formData = $(this).serialize();

    $("input[type=submit]").hide();
    $("input[type=button]").hide();
    $("div.message").addClass('wait');
    $("div.message").html( i18n.pleaseWait );
    $("div.message").show();

    $.ajax({
      type: 'POST',
      url: '/checkout_gothia.php?action=perform_flow',
      data: formData,
      dataType: 'json',
      success: function( response ) {
        if ( response.error )
        {
          $("div.message").removeClass('wait');
          $("div.message").addClass('error');
          $("div.message").html(response.error_txt);
          $("input[type=submit]").show();
          $("input[type=button]").show();
        }
        else
        {
          $("div.message").html(response.content);
          $(".help").hide();
          $("#gothia_payment_form").hide();
          if ( response.action == 'process' )
          {
            $("#checkout_process").submit();
          }
        }
      }
    });
  });

  $("#gothia_cancel_reservation").submit(function(e) {
    e.preventDefault();
    $.ajax({
      type: 'POST',
      url: '/checkout_gothia.php?action=cancel_reservation',
      dataType: 'json',
      success: function( response ) {
        if ( response.error )
        {
          $("div.message").addClass('error');
          $("div.message").html(response.error_txt);
        }
        else
        {
          $("div.message").addClass('status');
          $("div.message").html(response.content);
        }
      }
    });
  });

  $("#gothia_action_cancel_form").click(function(e) {
    e.preventDefault();
    $.ajax({
      type: 'POST',
      url: '/checkout_gothia.php?action=cancel_form',
      dataType: 'json',
      async: false,
      success: function( response ) {
        if ( response.error )
        {
          $("div.message").addClass('error');
          $("div.message").html(response.error_txt);
        }
        else
        {
          setTimeout(function(){ window.location = '/checkout_payment.php';},1000);
        }
      }
    });

  });
});
