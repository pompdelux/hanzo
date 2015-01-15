var mailplatform = (function($) {
  'use strict';
  var pub = {};

  pub.initAccountIframe = function() {
    $("#js-mailplatform-open-account").click(function(e) {
      e.preventDefault();
      $(this).attr('href', 'https://www.mailmailmail.net/');
      $(this).colorbox({iframe:true, width:"80%", height:"80%"});
    });
  };

  return pub;
})(jQuery);

if ($("#js-mailplatform-open-account").length) {
  mailplatform.initAccountIframe();
}
