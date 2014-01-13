var consultantNewsletter = (function($) {
  var pub = {};

  pub.init = function() {
    var handleNewsletterSubmit = function(e) {
      e.preventDefault();
      // warn the user before sending the emails.
      $this = $(this);
      dialoug.confirm(Translator.get('js:notice'), Translator.get('js:consultant.newsletter.confirm.send'), function(choice) {
        if (choice == 'ok') {
          tinyMCE.triggerSave();

          var actionName = $this.attr('name');
          var action = actionName + "=" + $this.val(); // to get the used submit button
          var formData = $("#consultant-newsletter").serialize();

          $.ajax({
            type: 'POST',
            url: base_url + 'consultantnewsletter/sendnewsletter',
            data: action +"&"+ formData,
            dataType: 'json',
            success: function(response, textStatus, jqXHR) {
              if (false === response.status) {
                if (response.message) {
                  dialoug.alert(Translator.get('js:notice'), response.message);
                }
              } else {
                window.scrollTo(window.scrollMinX, window.scrollMinY);
                dialoug.slideNotice(response.message);
                if(actionName !== 'actionSendTest'){
                  window.location.href = base_url + 'consultantnewsletter/history';
                }
              }
            }
          });
        }
      });
    };

    $("#actionCreateNewsletter").click( handleNewsletterSubmit );
    $("#actionSendTest").click( handleNewsletterSubmit );
    $("#consultant-newsletter").submit( function(e){e.preventDefault();} );

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
              dialoug.alert(Translator.get('js:notice'), response.message);
            }
          } else {
            window.scrollTo(window.scrollMinX, window.scrollMinY);
            dialoug.slideNotice(response.message);
            $('#users').val('');
          }
        }
      });
    });

    $('#history li .newsletter').click(function(e){
      e.preventDefault();
      var $html = $(this).parent().find('.colorbox').html();
      var defaults = {
        'close' : Translator.get('js:close'),
        'overlayClose' : true,
        'escKey' : true,
        'html' : '<div class="dialoug alert info">' + $html + '</div>'
      };
      $.colorbox(defaults);
    });

    $('#savedraft').click(function(e){
      e.preventDefault();
      var formData = $("#consultant-newsletter").serialize();
      $.ajax({
        type: 'POST',
        url: base_url + 'consultantnewsletter/draft/save',
        data: formData,
        dataType: 'json',
        success: function(response, textStatus, jqXHR) {
          if (false === response.status) {
            if (response.message) {
              dialoug.alert(Translator.get('js:notice'), response.message);
            }
          } else {
            window.location.href = base_url + 'consultantnewsletter/' + response.id;
          }
        }
      });
    });

    $('#drafts a.delete').click(function(e){
      e.preventDefault();
      var $a = $(this);

      // warn the user before removing the product.
      dialoug.confirm(Translator.get('js:notice'), Translator.get('js:consultant.newsletter.delete.draft'), function(choice) {
        if (choice == 'ok') {
          $.ajax({
            url : $a.attr('href'),
            dataType: 'json',
            async : false,
            success : function(response, textStatus, jqXHR) {
              if (response.status) {
                // add effects to the removal of a basket row
                $a.closest('li').fadeOut(function() {
                  $(this).remove();
                });
              }
            }
          });
        }
      });
    });

    $('#body-consultant-newsletter table.addresses a.delete').click(function(e){
      e.preventDefault();
      var $a = $(this);

      // warn the user before removing the product.
      dialoug.confirm(Translator.get('js:notice'), Translator.get('js:consultant.newsletter.delete.user'), function(choice) {
        if (choice == 'ok') {
          $.ajax({
            url : $a.attr('href'),
            dataType: 'json',
            async : false,
            success : function(response, textStatus, jqXHR) {
              if (response.status) {
                // add effects to the removal of a basket row
                $a.closest('tr').fadeOut(function() {
                  $(this).remove();
                });
              }
            }
          });
        }
      });
    });
  };

  return pub;
})(jQuery);

if ($("#body-consultant-newsletter").length) {
  consultantNewsletter.init();
}
