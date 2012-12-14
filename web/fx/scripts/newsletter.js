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

  return pub;
})(jQuery);
