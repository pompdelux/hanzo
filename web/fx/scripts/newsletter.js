/*global base_url:true*/
var newsletter = (function($) {
  var pub = {
    lists: {}
  };

  var is_initialized = false;

  var newsletter_jsonp_url;
  var $selector = '';
  var selectorName = '';

  pub.init = function( url ) {
    if ( is_initialized ) {
      return;
    }
    newsletter_jsonp_url = url;
    selectorName = "#newsletter-lists-container";
    $selector = $( selectorName );
    attachEvents();
    is_initialized = true;
  };

  pub.reset = function() {
    is_initialized = false;
    newsletter_jsonp_url = null;
    $selector = '';
    selectorName = '';
  };

  function subscriptionsUpdate( email, lists, action ) {
    dialoug.loading( selectorName, Translator.get('js:please.wait') );

    $.getJSON(newsletter_jsonp_url, {method: 'subscriptions:'+action, email: email, lists: lists}, function(data) {
      if ( data.is_error ) {
        dialoug.error( Translator.get('js:an.error.occurred'), Translator.get('js:'+data.content.msg, { 'email': email }) );
      }
      else {
        $.post(base_url+'newsletter/'+action, {email:email}); // send mail to customer
        dialoug.notice( Translator.get('js:action.completed' ), 'info', 2000 );
      }
      dialoug.stopLoading();
    });
  }

  function attachEvents() {
    $(selectorName+" form input[type=submit]").click(function(event) {
      event.preventDefault();

      var lists = [];
      var action = $( this ).data('action');
      var email = getEmail();

      $selector.find('.newsletter-list-id').each(function() {
        if ( $(this).attr('type') === 'checkbox' && $(this).is(":checked") ) {
          lists.push($(this).val());
        }
        else if( $(this).attr('type') !== 'checkbox' ) {
          lists.push($(this).val());
        }
      });

      subscriptionsUpdate( email, lists, action);
      lists = [];
    });
  }

  function getEmail() {
    return $selector.find('.newsletter-subscriber-email').val();
  }

  pub.lists.get = function( id ) {
    dialoug.loading( selectorName, Translator.get('js:please.wait') );
    var email = getEmail();

    $.getJSON(newsletter_jsonp_url, {method: 'lists:get', email: email}, function(data) {
      if ( data.is_error ) {
        dialoug.error( Translator.get('js:an.error.occurred'), data.content.msg  );
      }
      else {
        var $el = $(selectorName+' ul');
        var list_container = '<ul>';

        $.each(data.content.lists, function(index,list) {
          if ( ( id !== undefined && id == list.id ) || id === undefined ) { /* must use == to compare id and list.id */
            list_container += '<li><input type="checkbox" class="newsletter-list-id" name="listid[]" value="'+list.id+'" '+ (list.is_subscribed ? 'checked' : '') +'> '+list.name+'</li>';
          }
        });

        list_container += '</ul>';

        $el.replaceWith(list_container);
      }
      dialoug.stopLoading();
    });
  };

  pub.footer = function() {
    $('footer .footer-menu-4 form').on('submit', function(event) {
      event.preventDefault();

      var $form = $(this);
      $('input[type="email"]', $form).removeClass('error');

      $.post(this.action, $form.serialize(), function(response) {
        var type = (response.status? 'info' : 'error');
        dialoug.notice(response.message, type, 3000, $form);

        if (response.status) {
          $('input[type="text"], input[type="email"]', $form).val('');
        } else {
          $('input[type="email"]', $form).addClass('error');
        }
      }, 'json');
    });
  };

  pub.allover = function() {

//    // handeling .dk frontpage newsletter modal.
//    if ($('#newsletterModal').length) {
//        if (!$.cookie('newsletter_prompt_off') && (false === $('body').hasClass('is-mobile'))) {
//            $('#newsletterModal').show();
//        } else {
//            $('#openNewsletterModal').fadeIn();
//        }
//        $('#newsletterModal a.close-button').on('click', function(event) {
//            event.preventDefault();
//            $('#newsletterModal').hide();
//            $('#openNewsletterModal').fadeIn();
//            $.cookie('newsletter_prompt_off', 1, {expires : 3650});
//        });
//        $('#openNewsletterModal a').on('click', function(e){
//          e.preventDefault();
//          $('#openNewsletterModal').hide();
//          $('#newsletterModal').fadeIn();
//        });
//    }

    $('.js-newsletter-form .button').on('click', function(event) {
      event.preventDefault();
      var $this = $(this);
      var $form = $this.closest('form');
      var a = $this.data('action');
      var u = $form.attr('action').replace(/[un]?subscribe/, a);

      $.post(u, $form.serialize(), function(response) {
        var type = (response.status? 'info' : 'error');
        dialoug.notice(response.message, type, 3000, $form);

        if (response.status) {
          $('input[type="text"], input[type="email"]', $form).val('');
          $('input[type="text"], input[type="email"]', $form).removeClass('error');
          $form.addClass('newsletter-subscribe-complete');
        } else {
          $('input[type="email"]', $form).addClass('error');
        }
      }, 'json');
    });
  };

  return pub;
})(jQuery);

newsletter.footer();
newsletter.allover();
