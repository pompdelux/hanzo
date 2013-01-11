(function(doc, $) {

  var category = (function($) {
    var pub = {};

    var History = window.History;
    var title = document.title;
    var cache = [];
    var killScroll = false; // IMPORTANT

    var $target = $('div.' + $('.pager.ajax').data('target'));

    pub.initPager = function() {

      if (0 === $target.length) { return; }

      var matches = document.location.href.match(/[0-9]$/);
      var current_pager_id = matches ? matches[0] : 1;

      yatzy.compile('productItems');

      // Detect that we are at the infinite-scroll-trigger, and call autoloading function
      $(window).scroll(function(){ // IMPORTANT
        if ($(window).scrollTop()+$(window).height()+200 >= ($('.infinite-scroll-trigger').offset().top)){ // IMPORTANT
          if (killScroll === false) { // IMPORTANT - Keeps the loader from fetching more than once.
            killScroll = true; // IMPORTANT - Set killScroll to true, to make sure we do not trigger this code again before it's done running.
            $current = $('a' ,$('.pager.ajax li.current').next());
            var t = title+'/'+ $current.attr('title') || null;

            History.pushState({}, t, $current.attr('href'));
          }
        }
      });

      $(window).on('statechange', function(event) {
        var
          State = History.getState(),
          url = State.url;

        // start loading anim
        dialoug.loading('.pager.ajax ul', Translator.get('js:loading.std'));

        // fetch unknown pages via ajax
        if ( cache[url] === undefined ) {
          $.ajax({
            url : url,
            data : {_xjson : true},
            dataType: 'json',
            async : false,
            beforeSend: function(jqXHR){
              jqXHR.setRequestHeader('X-PJAX', 'true');
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

        var current = cache[url];

        // append the result to the document
        $target.append(yatzy.render('productItems', current.products));

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
        killScroll = false; // Turn on infinite scroll again
        // trigger google analytics - if available
        if ( undefined !== window._gaq ) {
          _gaq.push(['_trackPageview']);
        }
      });
    };

    pub.initFlip = function() {
      var originalPicture = null;
      $(document).on({
        mouseenter: function () {
          if($(this).data('flip')){
            originalPicture = $(this).attr('src');
            $(this).attr('src', $(this).data('flip'));
          }
        },
        mouseleave: function () {
          if(originalPicture){
            $(this).attr('src',originalPicture);
            originalPicture = null;
          }
        }
      }, 'img.flip');
    };
    return pub;
  })(jQuery);

  category.initPager();
  category.initFlip();

})(document, jQuery);
