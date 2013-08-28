/*
 * @copyright bellcom open source aps
 * @author ulrik nielsen <un@bellcom.dk>
 */

(function(jQuery) {
  jQuery.fn.simpleautogrow = function() {
    return this.each(function() {
      new jQuery.simpleautogrow(this);
    });
  };
  jQuery.simpleautogrow = function (e) {
    var self = this;
    var $e = this.textarea = jQuery(e).css({overflow: 'hidden', display: 'block'}).bind('focus', function() {
      this.timer = window.setInterval(function() {
        self.checkExpand();
      }, 100);
    }).bind('blur', function() {
      clearInterval(this.timer);
    });
    this.border = $e.outerHeight() - $e.innerHeight();
    this.clone = $e.clone().css({position: 'absolute', visibility: 'hidden'}).attr('name', '');
    $e.height(e.scrollHeight + this.border).after(this.clone);
    this.checkExpand();
  };
  jQuery.simpleautogrow.prototype.checkExpand = function() {
    var target_height = this.clone[0].scrollHeight + this.border;
    if (this.textarea.outerHeight() != target_height) {
      this.textarea.height(target_height + 'px');
    }
    this.clone.attr('value', this.textarea.attr('value')).height(0);
  };
})(jQuery);

var wall = (function($) {
  var pub = {};
  var $target = $('div#wall-posts');

  pub.init = function() {
    jQuery.data(document.body, 'page', 1);
    jQuery.data(document.body, 'locked', false);

    yatzy.compile('postItems');
    getWallPosts();
  };

  pub.avatarFallback = function(img) {
    if (img.src.indexOf('JohnDoe') == -1) {
      img.src = img.src.replace(/[0-9]+/, 'JohnDoe');
    }
  };

  function getWallPosts() {
  if (false === jQuery.data(document.body, 'locked')) {
    jQuery.data(document.body, 'locked', true);
    var pager = jQuery.data(document.body, 'page');
    $('#wall-posts').append('<div class="wall-loading-data"><span>Henter...</span> <img src="'+cdn_url+'fx/images/wall/ajax-loader-large.gif" alt="" /></div>');
    $.ajax({
      url : base_url + 'wall/get-wall/' + pager,
      data : {_xjson : true},
      dataType: 'json',
      async : false,
      cache: false,
      beforeSend: function(jqXHR){
        jqXHR.setRequestHeader('X-PJAX', 'true');
      },
      success : function(response, textStatus, jqXHR) {
        if(jqXHR.getResponseHeader('X-JSON')) {
          var headers = jQuery.parseJSON(jqXHR.getResponseHeader('X-JSON'));
          if (headers.status) {
            //cache[url] = response;
            // append the result to the document
            var result = yatzy.render('postItems', response.data);
            $target.append(result);
            jQuery.data(document.body, 'page', pager + 1);
            jQuery.data(document.body, 'locked', false);
          }else{
            jQuery.data(document.body, 'locked', true);
          }
        }
      },
      error: function() {
        jQuery.data(document.body, 'locked', true);
      }
    });
    $('div.wall-loading-data').remove();
  }
  }

  $('div.like').on('click', function(e) {
    $.colorbox({
      'top' : '25%',
      'width' : '50%',
      'height' : '50%',
      'close' : Translator.get('js:close'),
      'html': '<div class="dialoug alert %type%"><h2>%title%</h2>%message%</div>'
              .replace('%title%', Translator.get('js:wall.like.popup.header'))
              .replace('%message%', $(this).find('div.like-pop').html())
              .replace('%type%', 'like-pop')
    });
  });

  $(window).scroll(function(){
    if (($(window).scrollTop()) == $(document).height() - $(window).height()) {
      getWallPosts();
    }
  });

  return pub;
})(jQuery);


$(function() {

  if ($("div#wall-posts").length) {
    wall.init();
  }

  $('textarea.grow[title]').each(function() {
    if (this.value === '') {
      this.value = this.title;
    }
    $(this).focus(function() {
      if(this.value == this.title) {
        $(this).val('').addClass('focused');
      }
      $(this).parent().parent().find('dd input').show();
    });
    $(this).blur(function() {
      if(this.value === '') {
        $(this).val($(this).attr('title')).removeClass('focused');
        $(this).parent().parent().find('dd input').hide();
      }
    });
  });

  $('.wall-subpost .show-all').on('click', function() {
    $(this).next('div.hide').slideDown();
    $(this).remove();
  });

  $('textarea.grow').simpleautogrow();
  $('textarea.grow:hidden').css({top : "-2000px", left : "-2000px"});

  $('div.wall-post a[rel="wall-comment"]').on('click', function(e){
    e.preventDefault();
    $form = $($(this).parent().find('.dialoug').html());
    $.colorbox({
      'top' : '25%',
      'width' : '50%',
      'height' : '50%',
      'close' : Translator.get('js:close'),
      'html': $form
    }, function(){
      $form.find('textarea.grow').simpleautogrow();
      $form.submit(function(e){
        e.preventDefault();
        $data = $(this).serialize();
        $.ajax({
          url: $(this).attr('action'),
          dataType: 'json',
          type: 'POST',
          data: $data,
          async: false,
          cache: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(Translator.get('js:notice', response.message));
              }
            } else {
              var result = yatzy.render('subPostItems', response.data);
              $('#wall-post-'+response.data[0].parent_id+' .comments').append(result);
              $.colorbox.close();
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
          }
        });
      });
    });
  });

  $('div.wall-post a[rel="wall-edit"]').on('click', function(e){
    e.preventDefault();
    $form = $($(this).parent().find('.dialoug').html());
    $.colorbox({
      'top' : '25%',
      'width' : '50%',
      'height' : '50%',
      'close' : Translator.get('js:close'),
      'html': $form
    }, function(){
      $form.find('textarea.grow').simpleautogrow();
      $form.submit(function(e){
        e.preventDefault();
        $data = $(this).serialize();
        $.ajax({
          url: $(this).attr('action'),
          dataType: 'json',
          type: 'POST',
          data: $data,
          async: false,
          cache: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(Translator.get('js:notice', response.message));
              }
            } else {
              $('#wall-post-'+response.id+' .content').text(response.input);
              $.colorbox.close();
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
          }
        });
      });
    });
  });

  // $('.wall-subentry-form').submit(function(e){
  $('#wall-entry-form').submit(function(e){
    e.preventDefault();
    $data = $(this).serialize();
    $.ajax({
      url: $(this).attr('action'),
      dataType: 'json',
      type: 'POST',
      data: $data,
      async: false,
      cache: false,
      success: function(response, textStatus, jqXHR) {
        if (false === response.status) {
          if (response.message) {
            dialoug.alert(Translator.get('js:notice', response.message));
          }
        } else {
          var result = yatzy.render('postItems', response.data);
          $('#wall-posts').prepend(result);
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
      }
    });
  });

  $('div.wall-post a[rel="wall-like"]').on('click', function(e){
    e.preventDefault();
    $a = $(this);
    $.ajax({
      url: $(this).attr('href'),
      dataType: 'json',
      type: 'GET',
      async: false,
      cache: false,
      success: function(response, textStatus, jqXHR) {
        if (false === response.status) {
          if (response.message) {
            dialoug.alert(Translator.get('js:notice', response.message));
          }
        } else {
           $a.find('span').toggleClass('show').toggleClass('hidden');
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
      }
    });
  });

  $('div.wall-post a[rel="wall-delete"]').on('click', function(e){
    e.preventDefault();
    $a = $(this);
    dialoug.confirm(Translator.get('js:notice'), Translator.get('js:wall.delete.entry.confirm'),function(choice) {
      if (choice == 'ok') {
        $.ajax({
          url: $a.attr('href'),
          dataType: 'json',
          type: 'GET',
          async: false,
          cache: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(Translator.get('js:notice', response.message));
              }
            } else {
              $a.parent().parent().parent().parent().fadeOut('slow', function() {
                $(this).remove();
              });
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
          }
        });
      }
    });
  });
});
