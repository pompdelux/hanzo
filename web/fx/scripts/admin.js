(function($) {


  var gui = (function($) {
    var pub = {}

    pub.initUI = function() {

      $("#select-cache a.open-menu").click(function(e) {
        e.preventDefault();
        $("#select-cache div").slideToggle();
      });

      $("#select-domain a.open-menu").click(function(e) {
        e.preventDefault();
        $("#select-domain div").slideToggle();
      });

      $("#select-language a.open-menu").click(function(e) {
        e.preventDefault();
        $("#select-language div").slideToggle();
      });

      /* Admin Sortable list to show and update Cms pages' order*/
      $('#sortable-list').nestedSortable({
        listType: 'ul',
        handle: 'div',
        helper: 'clone',
        items: 'li',
        opacity: 0.5,
        placeholder: 'helper',
        forcePlaceholderSize: true,
        errorClass: 'err',
        maxLevels: 5,
        tolerance: 'pointer',
        toleranceElement: '> div',
        update: function () {
          list = $(this).nestedSortable('toArray', {startDepthCount: 0});
          $.ajax({
            url: 'update-tree/',
            dataType: 'json',
            type: 'POST',
            data: {data : list},
            async: false,
            success: function(responce, textStatus, jqXHR) {
              if (false === responce.status) {
                if (responce.message) {
                  dialoug.alert(i18n.t('Notice!', responce.message));
                }
              }
              else {
                window.scrollTo(window.scrollMinX, window.scrollMinY);
                dialoug.slideNotice(responce.message);
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              dialoug.error(i18n.t('Notice!'),i18n.t('An error occurred'));
            }
          });
        }
      });
      /* Delete cms node from NestedSortable */
      $('a.delete').click(function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(i18n.t('Notice!'), i18n.t('Er du sikker på du vil <strong>slette</strong> CMS noden?'),function(choice) {
          if (choice == 'ok') {
            $.ajax({
              url : $a.attr('href'),
              dataType: 'json',
              async : false,
              success : function(response, textStatus, jqXHR) {
                if (response.status) {
                  $a.parent().parent().parent().fadeOut(function() {
                    $(this).remove();
                  });

                  window.scrollTo(window.scrollMinX, window.scrollMinY);
                  dialoug.slideNotice(response.message);
                }
              }
            });
          }
        });
      });

      // ios class added to body
      switch (navigator.platform) {
      case 'iPad':
      case 'iPhone':
      case 'iPod':
        $('html').addClass('ios');
        break;
      }
    }

    /**
     * animated effect on first menu level
     */
    pub.initAnimatedMenu = function() {
      $('nav.main > ul > li > a').hover(function() {
        var $this = $(this);
        var left = $this.data('pl-save');
        if (left == undefined) {
          left = parseInt($this.css('padding-left'));
          $this.data('plsave', left);
        }
        $this.animate({
          'padding-left': (left + 10) + 'px'
        }, 'fast');
      }, function() {
        var $this = $(this);
        $this.animate({
          'padding-left': $this.data('plsave') + 'px'
        }, 'fast');
      });
    }


    return pub;
  })(jQuery);

  gui.initUI();
  gui.initAnimatedMenu();

})(document, jQuery);
