var consultantNewsletter = (function($) {
  var pub = {};
  //TEST ANDERS 222222w

  pub.init = function() {
    var handleNewsletterSubmit = function(e) {
      e.preventDefault();
      tinyMCE.triggerSave();

      var actionName = $(this).attr('name');
      var action = actionName + "=" + $(this).val(); // to get the used submit button
      var formData = $("#consultant-newsletter").serialize();

      $.ajax({
        type: 'POST',
        url: base_url + 'consultantnewsletter/sendnewsletter',
        data: action +"&"+ formData,
        dataType: 'json',
        success: function(response, textStatus, jqXHR) {
          if (false === response.status) {
            if (response.message) {
              dialoug.alert(ExposeTranslation.get('js:notice'), response.message);
            }
          } else {
            window.scrollTo(window.scrollMinX, window.scrollMinY);
            dialoug.slideNotice(response.message);
          }
        },
      });
    };
    $("#actionCreateNewsletter").click( handleNewsletterSubmit );
    $("#actionSendTest").click( handleNewsletterSubmit );

    $('#consultant-newsletter-import').submit(function(e){
      e.preventDefault();

      $.ajax({
        type: 'POST',
        url: base_url + 'consultantnewsletter/doimportusers',
        data: $('#consultant-newsletter-import').serialize(),
        dataType: 'json',
        success: function(response, textStatus, jqXHR) {
          if (false === response.status) {
            if (response.message) {
              dialoug.alert(ExposeTranslation.get('js:notice'), response.message);
            }
          } else {
            window.scrollTo(window.scrollMinX, window.scrollMinY);
            dialoug.slideNotice(response.message);
            $('#users').val('');
          }
        },
      });
    };
    });
  };
 
  return pub;
})(jQuery);

if ($("#body-consultant-newsletter").length) {
  consultantNewsletter.init();
}