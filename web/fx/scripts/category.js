(function(doc, $) {

  var category = (function($) {
    var pub = {}

    var History = window.History;
    var title = document.title;
    var cache = [];

    var $target = $('div.' + $('.pager.ajax').data('target'));

    pub.initPager = function() {

      if (0 == $target.length) { return; }

      var matches = document.location.href.match(/[0-9]$/);
      var currtent_pager_id = matches ? matches[0] : 1;;

      yatzy.compile('productItems');

      $('.pager.ajax a').on('click', function(event) {
        if ( event.which == 2 || event.metaKey ) {
          return true;
        }
        event.preventDefault();

        var t = title+' / '+this.title || null;

        History.pushState({}, t, this.href);
      });

      $(window).on('statechange', function(event) {
        var
          State = History.getState(),
          url = State.url;

        // start loading anim
        dialoug.loading('.pager.ajax ul', i18n.t('Loading ...'));

        // fetch unknown pages via ajax
        if ( cache[url] === undefined ) {
          $.ajax({
            url : url,
            data : {_xjson : true},
            dataType: 'json',
            async : false,
            beforeSend: function(jqXHR){
              jqXHR.setRequestHeader('X-PJAX', 'true')
            },
            success : function(responce, textStatus, jqXHR) {
              if(jqXHR.getResponseHeader('X-JSON')) {
                var headers = jQuery.parseJSON(jqXHR.getResponseHeader('X-JSON'));
                if (headers.status) {
                  cache[url] = responce;
                }
              }
              else {
                // fallback to oldschool page views
                document.location.href = url;
                return false;
              }
            },
            error: function() {
              // fallback to oldschool page views
              document.location.href = url;
              return false;
            }
          });
        }

        // transition effects
        matches = url.match(/[0-9]$/);
        var vid = matches ? matches[0] : 1;

        // settings for the pager animation
        direction = 'next';
        var anim_params_in = {left : 0};
        var anim_params_out = {left : -744};

        if (currtent_pager_id > vid) {
          direction = 'prev';
          var anim_params_in = {left : 0};
          var anim_params_out = {left : 744};
        }

        currtent_pager_id = vid;
        var current = cache[url];

        // append the result to the document
        $target.append(yatzy.render('productItems', current.products));
        $new_item = $('.new-item', $target);
        $new_item.addClass(direction);

        // run the swithc page animation
        $('.old-item', $target).animate(anim_params_out, function() {
          $(this).remove();
        });
        $new_item.animate(anim_params_in, function() {
          $new_item.addClass('old-item').removeClass('new-item').removeClass(direction);
        });

        // setup pager links
        var $next = $('.pager.ajax li.next');
        var $prew = $('.pager.ajax li.prew');

        $('.pager.ajax li').removeClass('current');
        $('.pager.ajax li:eq(' + current.paginate.index + ')').addClass('current');

        $next.addClass('off');
        $prew.addClass('off');

        $next.children('a').attr('href', current.paginate.next);
        $prew.children('a').attr('href', current.paginate.prew);

        // switch on/off next and prev links
        if ((undefined !== current.paginate.next) && current.paginate.next) {
          $next.removeClass('off');
        }
        if ((undefined !== current.paginate.prew) && current.paginate.prew) {
          $prew.removeClass('off');
        }

        // stop loading anim
        dialoug.stopLoading();

        // trigger google analytics - if available
        if ( undefined !== window._gaq ) {
          _gaq.push(['_trackPageview']);
        }
      });
    };

    return pub;
  })(jQuery);

  category.initPager();

})(document, jQuery);
