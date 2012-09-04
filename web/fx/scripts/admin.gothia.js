var admin_gothia = (function($) {
  var pub = {};

  var current_order_id = null;

  pub.init = function() {
  
    $("#gothia-admin-order-lookup").submit(function(e) {
      e.preventDefault();

      if ( $("#order-id").val() === '' ) {
        alert('Manglende ordre id');
        return false;
      }

      var data = $(this).serialize();
      var customer_html = '';
      var order_html = '';
      $.ajax({
        url: base_url+'orders/gothia/get-order',
        type: 'post',
        dataType: 'json',
        data: data,
        success: function(data) {
          if ( data.status ) {
            customer_html = data.data.order.customer.name+'<br>';
            order_html = 'ID: '+data.data.order.id+'<br>Beløb: '+data.data.order.amount+'<br>State: '+data.data.order.state;
            current_order_id = data.data.order.id;

            $("#gothia-admin-order-perform .customer").html(customer_html);
            $("#gothia-admin-order-perform .order").html(order_html);
            $("#gothia-admin-order-perform").slideDown();
          }
          else {
            current_order_id = null;
            $("#gothia-admin-order-perform .customer").html();
            $("#gothia-admin-order-perform .order").html();
            $("#gothia-admin-order-perform").slideUp();
            alert(data.message);
          }
        }
      });
    });

    $("#place-reservation").click(function(e) {
      e.preventDefault();

      var data = {'order-id': current_order_id};

      $.ajax({
        url: base_url+'orders/gothia/place-reservation',
        type: 'post',
        dataType: 'json',
        data: data,
        success: function(data) {
          alert(data.message);
        }
      });
    });

    $("#cancel-reservation").click(function(e) {
      e.preventDefault();

      var data = {'order-id': current_order_id};

      $.ajax({
        url: base_url+'orders/gothia/cancel-reservation',
        type: 'post',
        dataType: 'json',
        data: data,
        success: function(data) {
          alert(data.message);
        }
      });
    });

  };

  return pub;
})(jQuery);
