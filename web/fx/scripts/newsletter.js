var newsletter = (function($) {
  var base_url = '';
  var email = '';
  var pub = {
    lists: {}
  };

  var $selector;
  var selectorName;

  pub.init = function( selector ) {
    $selector = $(selector);
    selectorName = selector;
    email = $selector.find('.newsletter-subscriber-email').val();
    base_url = $selector.find('form').attr('action');
    attachEvents();
  };

  var subscriptionsUpdate = function( lists ) {
    dialoug.loading( selectorName, i18n.t('Please wait') );
    console.log( 'email: '+email+' lists: '+lists );
    $.getJSON(base_url, {method: 'subscriptions:update', email: email, lists: lists}, function(data) {
      if ( data.is_error )
      {
        dialoug.error( i18n.t('An error occurred'), data.content.msg  );
      }
      dialoug.stopLoading();
    });
  };

  var attachEvents = function() {
    var lists = [];
    $(selectorName+" form").on('submit',function(event) {
      event.preventDefault();
      
      if ( email === "" ) {
        email = $selector.find('.newsletter-subscriber-email').val();
      }

      $selector.find('.newsletter-list-id').each(function() {
        if ( $(this).attr('type') === 'checkbox' && $(this).is(":checked") )
        {
          lists.push($(this).val());
        }
        else if( $(this).attr('type') !== 'checkbox' )
        {
          lists.push($(this).val());
        }
      });

      subscriptionsUpdate(lists);
      lists = [];
    });
  };

  pub.lists.get = function( id ) {
      dialoug.loading( selectorName, i18n.t('Please wait') );
      $.getJSON(base_url, {method: 'lists:get', email: email}, function(data) {
      if ( data.is_error )
      {
        dialoug.error( i18n.t('An error occurred'), data.content.msg  );
      }
      else
      {
        var $el = $(selectorName+' ul');
        var list_container = '<ul>';
        
        $.each(data.content.lists, function(index,list) {
          if ( ( id !== undefined && id == list.id ) || id === undefined ) /* must use == to compare id and list.id */
          {
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
