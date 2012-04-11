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

      $("#select-category a.open-menu").click(function(e) {
        e.preventDefault();
        $("#select-category div").slideToggle();
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
        toleranceElement: '> div'
      });

      $('#save-nestedsortable').click(function(e){
        e.preventDefault();
        list = $('ul#sortable-list').nestedSortable('toArray', {startDepthCount: 0});
        console.log(list);
        $.ajax({
          url: 'update-tree/',
          dataType: 'json',
          type: 'POST',
          data: {data : list},
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(i18n.t('Notice!', response.message));
              }
            }
            else {
              window.scrollTo(window.scrollMinX, window.scrollMinY);
              dialoug.slideNotice(response.message);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(i18n.t('Notice!'),i18n.t('An error occurred'));
          }
        });
      });
      
      /* Delete cms node from NestedSortable */
      $('#sortable-list a.delete').click(function(e){
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
      // Sortable list for products ind a category
      $('ul#product-list-sort').sortable({
        axis : 'y',
        delay : 500,
        distance : 30,
        forceHelperSize : true,
        forcePlaceholderSize : true,
        items : 'li',
        opacity : 0.5,
        placeholder : 'placeholder',
        scroll: true,
      });
      $('#save-sortable').click(function(e){
        e.preventDefault();
        list = $('ul#product-list-sort').sortable('toArray');
        $.ajax({
          url: '../../update-sort/',
          dataType: 'json',
          type: 'POST',
          data: {data : list},
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(i18n.t('Notice!', response.message));
              }
            }
            else {
              window.scrollTo(window.scrollMinX, window.scrollMinY);
              dialoug.slideNotice(response.message);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(i18n.t('Notice!'),i18n.t('An error occurred'));
          }
        });
      });

      //ProductsToImages on Products page
      $('.product-selector').change(function(){
        selectedOption = $(this).find('option:selected');
        reference = selectedOption.val().split('-');
        image = reference[0];
        product = reference[1];
        $.ajax({
          url: '../add-reference/',
          dataType: 'json',
          type: 'POST',
          data: {image : image, product : product},
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(i18n.t('Notice!', response.message));
              }
            }
            else {
              $('#item-' + image + ' .product-references').append('<li><span class="actions"><a href="' + base_url + 'products/delete-reference/' + image + '/' + product + '" class="delete" title="Slet">Slet</a></span><span class="id">(#' + product + ')</span><span class="sku">' + selectedOption.text() + '</span></li>');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(i18n.t('Notice!'),i18n.t('An error occurred'));
          }
        });
        $(this).val(0);
      });


      $('#product-images-list a.delete').live('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(i18n.t('Notice!'), i18n.t('Er du sikker på du vil <strong>slette</strong> Billede referencen?'),function(choice) {
          if (choice == 'ok') {
            $.ajax({
              url : $a.attr('href'),
              dataType: 'json',
              async : false,
              success : function(response, textStatus, jqXHR) {
                if (response.status) {
                  $a.parent().parent().fadeOut(function() {
                    $(this).remove();
                  });
                }
              }
            });
          }
        });
      });

      // CategoriesToProducts on Products page
      $('#product-category-selector').change(function(){
        selectedOption = $(this).find('option:selected');
        reference = selectedOption.val().split('-');
        category = reference[0];
        product = reference[1];
        $.ajax({
          url: '../add-category/',
          dataType: 'json',
          type: 'POST',
          data: {category : category, product : product},
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(i18n.t('Notice!', response.message));
              }
            }
            else {
              $('#product-categories').append('<li><span class="actions"><a href="' + base_url + 'products/delete-category/' + category + '/' + product + '" class="delete" title="Slet">Slet</a></span><span class="id">(#' + category + ')</span><span class="title"> ' + selectedOption.text() + '</span></li>');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(i18n.t('Notice!'),i18n.t('An error occurred'));
          }
        });
        $(this).val(0);
      });


      $('#product-categories a.delete').live('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(i18n.t('Notice!'), i18n.t('Er du sikker på du vil <strong>slette</strong> Kategorien fra produktet  ?'),function(choice) {
          if (choice == 'ok') {
            $.ajax({
              url : $a.attr('href'),
              dataType: 'json',
              async : false,
              success : function(response, textStatus, jqXHR) {
                if (response.status) {
                  $a.parent().parent().fadeOut(function() {
                    $(this).remove();
                  });
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
