var newsletter = (function($) {
  var base_url = 'http://phplist.pompdelux.dk/integration/json.php?callback=?';
  var email = 'hf@bellcom.dk';
  var pub = {
    lists: {}
  };

  pub.init = function() {
  };

  var subscriptionsUpdate = function( lists ) {
    $.getJSON(base_url, {method: 'subscriptions:update', email: email, lists: lists}, function(data) {
      console.log(data);
    });
  };

  var attachEvents = function() {
    $("#newsletter-lists-container ul li span").on('click',function(event) {
      /* FIXME: */
      console.log( $(this).data('state') );
      var lists = [ $(this).data('listid') ];
      subscriptionsUpdate(lists);
    });
  };

  pub.lists.get = function() {
    $.getJSON(base_url, {method: 'lists:get', email: email}, function(data) {
      if ( data.is_error )
      {
        console.log(data.msg);
      }
      else
      {
        var $el = $('#newsletter-lists-container ul');
        var list_container = '<ul>';
        
        $.each(data.content.lists, function(index,list) {
          list_container += '<li><span data-listid="'+list.id+'" data-state="'+ (list.is_subscribed ? '1">[-' : '0">[+') +']</span> '+list.name+'</li>';
        });

        list_container += '</ul>';

        $el.replaceWith(list_container);
        attachEvents();
      }
    });
  };

  return pub;
})(jQuery);
