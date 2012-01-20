var gothia = (function($) {
  var pub = {};

  pub.init = function() {
  };

  pub.createAccountInit= function() {
      $("#gothia-account-container form").on('submit', function(event) {
        event.preventDefault();
        var data = $(this).serialize();
        $.ajax({
          url: '/app_dev.php/payment/gothia/test',
          type: 'post',
          dataType: 'json',
          data: data,
          success: function(data) {
              console.log(data);
          }
        });
      });
  };

  return pub;
})(jQuery);
