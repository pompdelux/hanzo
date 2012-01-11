(function(doc, $) {


  var category = (function($) {
    var pub = {}

    var History = window.History;
    var title = document.title;
    var cache = [];

    var $target = $('div.' + $('.pager.ajax').data('target'));

    pub.initPager = function() {
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

        var $loading = $('.pager .loading');
        var $loading_span = $loading.find('span');

        var loading = setInterval(function() {
          $loading.show();
          var w = $loading_span.width() + 2;
          $loading_span.width(w);
        }, 10);

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
                document.location.href = url;
                return false;
              }
            },
            error: function() {
              document.location.href = url;
              return false;
            }
          });
        }

        var current = cache[url];

        $target.append('<div class="wrapper"></div>');
        var $wrapper = $target.find('.wrapper').last();

        $.each(current.products, function(index, item) {
          if (undefined === item.prices.sales) {
            item.normal = item.prices.normal.formattet;
          }
          else {
            item.sales = item.prices.sales.formattet;
            item.normal = item.prices.normal.formattet;
          }

          item.index = index+1;
          $wrapper.append(ich.productItem(item));
        });

        clearInterval(loading);
        $loading_span.hide();
        $loading_span.width(1);

        $target.find('.wrapper').first().slideUp(function() {
          $(this).remove();

          var $next = $('.pager.ajax li.next');
          var $prew = $('.pager.ajax li.prew');

          $('.pager.ajax li').removeClass('current');
          $('.pager.ajax li:eq('+current.paginate.index+')').addClass('current');

          $next.addClass('off');
          $prew.addClass('off');

          $next.children('a').attr('href', current.paginate.next);
          $prew.children('a').attr('href', current.paginate.prew);

          if ((undefined !== current.paginate.next) && current.paginate.next) {
            $next.removeClass('off');
          }
          if ((undefined !== current.paginate.prew) && current.paginate.prew) {
            $prew.removeClass('off');
          }

        });


        if ( undefined !== window._gaq ) {
          _gaq.push(['_trackPageview']);
        }
      });
    };

    return pub;
  })(jQuery);

  category.initPager();

})(document, jQuery);
