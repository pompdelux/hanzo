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

      $("#select-code a.open-menu").click(function(e) {
        e.preventDefault();
        $("#select-code div").slideToggle();
      });

      $("#select-ns-add a.open-menu").click(function(e) {
        e.preventDefault();
        $("#select-ns-add div").slideToggle();
      });

      $('.datepicker').datepicker({
        dateFormat : "dd-mm-yy"
      });

      /* Cache controller */
      $('.cache-clear').click(function(e){
        e.preventDefault();
        var $a = $(this);
        $.ajax({
          url : $a.attr('href'),
          dataType: 'json',
          async : false,
          success : function(response, textStatus, jqXHR) {
            if (response.status) {
              window.scrollTo(window.scrollMinX, window.scrollMinY);
              dialoug.slideNotice(response.message);
            }
          }
        });
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
                dialoug.alert(ExposeTranslation.get('js:notice'), response.message);
              }
            } else {
              window.scrollTo(window.scrollMinX, window.scrollMinY);
              dialoug.slideNotice(response.message);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(ExposeTranslation.get('js:notice'), ExposeTranslation.get('js:an.error.occurred'));
          }
        });
      });

      /* Delete cms node from NestedSortable */
      $('#sortable-list a.delete').click(function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(ExposeTranslation.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> CMS noden?',function(choice) {
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
      $('#inactive_nodes a.delete').click(function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(ExposeTranslation.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> CMS noden?',function(choice) {
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

                  window.scrollTo(window.scrollMinX, window.scrollMinY);
                  dialoug.slideNotice(response.message);
                }
              }
            });
          }
        });
      });

      var $cms_path = $('#cms_path').val();
      // CMS edit page, title text field slug creation
      $('#cms_title').blur(function(e){
        var slug = new String();
        var chars = {
            'æ' : 'ae', 'Æ' : 'AE',
            'ø' : 'oe', 'Ø' : 'OE',
            'å' : 'aa', 'Å' : 'AA',
            'é' : 'e',  'É' : 'E', 'è' : 'e', 'È' : 'E',
            'à' : 'a',  'À' : 'A', 'ä' : 'a', 'Ä' : 'A', 'ã' : 'a', 'Ã' : 'A',
            'ò' : 'o',  'Ò' : 'O', 'ö' : 'o', 'Ö' : 'O', 'õ' : 'o', 'Õ' : 'O',
            'ù' : 'u',  'Ù' : 'U', 'ú' : 'u', 'Ú' : 'U', 'ũ' : 'u', 'Ũ' : 'U',
            'ì' : 'i',  'Ì' : 'I', 'í' : 'i', 'Í' : 'I', 'ĩ' : 'i', 'Ĩ' : 'I',
            'ß' : 'ss',
            'ý' : 'y', 'Ý' : 'Y',
            ' ' : '-',
            '/' : '-'
        };
        $title = $('#cms_title').val();

        for (var i = 0; i < $title.length; i++) {
            if ( chars[$title.charAt(i)] ) 
            {
              slug += chars[$title.charAt(i)]
            }
            else
            {
              slug += $title.charAt(i)
            }
        }
        slug = slug.replace(/[^-\w\s$\*\(\)\'\!\_]/g, '-');  // remove unneeded chars
        slug = slug.replace(/^\s+|\s+$/g, ''); // trim leading/trailing spaces
        slug = slug.replace(/[-\s]+/g, '-');   // convert spaces
        slug = slug.replace(/-$/, '');         // remove trailing separator
        slug = slug.toLowerCase();
        $('#cms_path').val($cms_path + slug);
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
                dialoug.alert(ExposeTranslation.get('js:notice', response.message));
              }
            }
            else {
              window.scrollTo(window.scrollMinX, window.scrollMinY);
              dialoug.slideNotice(response.message);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(ExposeTranslation.get('js:notice'), ExposeTranslation.get('js:an.error.occurred'));
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
                dialoug.alert(ExposeTranslation.get('js:notice', response.message));
              }
            }
            else {
              $('#item-' + image + ' .product-references').append('<li><span class="actions"><a href="' + base_url + 'products/delete-reference/' + image + '/' + product + '" class="delete" title="Slet">Slet</a></span><span class="id">(#' + product + ')</span><span class="sku">' + selectedOption.text() + '</span></li>');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(ExposeTranslation.get('js:notice'), ExposeTranslation.get('js:an.error.occurred'));
          }
        });
        $(this).val(0);
      });


      $('#product-images-list a.delete').live('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(ExposeTranslation.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> Billede referencen?',function(choice) {
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
                dialoug.alert(ExposeTranslation.get('js:notice', response.message));
              }
            }
            else {
              $('#product-categories').append('<li><span class="actions"><a href="' + base_url + 'products/delete-category/' + category + '/' + product + '" class="delete" title="Slet">Slet</a></span><span class="id">(#' + category + ')</span><span class="title"> ' + selectedOption.text() + '</span></li>');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(ExposeTranslation.get('js:notice'), ExposeTranslation.get('js:an.error.occurred'));
          }
        });
        $(this).val(0);
      });


      $('#product-categories a.delete').live('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(ExposeTranslation.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> Kategorien fra produktet ?',function(choice) {
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

      $('#translation-list a.delete').live('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(ExposeTranslation.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> oversættelsen til kategorien ?',function(choice) {
          if (choice == 'ok') {
            $.ajax({
              url : $a.attr('href'),
              dataType: 'json',
              async : false,
              success : function(response, textStatus, jqXHR) {
                if (response.status) {
                  $a.parent().fadeOut(function() {
                    $(this).remove();
                  });
                }
              }
            });
          }
        });
      });

      $('#category-list a.delete').live('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(ExposeTranslation.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> denne kategori ?',function(choice) {
          if (choice == 'ok') {
            $.ajax({
              url : $a.attr('href'),
              dataType: 'json',
              async : false,
              success : function(response, textStatus, jqXHR) {
                if (response.status) {
                  $a.parent().fadeOut(function() {
                    $(this).remove();
                  });
                }
              }
            });
          }
        });
      });

      $('#washing a.delete').live('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(ExposeTranslation.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> denne kategori ?', function(choice) {
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

      $('#messages a.delete').live('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(ExposeTranslation.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> denne besked ?',function(choice) {
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

      $('#redirects a.delete').live('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(ExposeTranslation.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> denne Redirect ?', function(choice) {
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

      $('#zip_to_city a.delete').live('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(ExposeTranslation.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> dette Post Nummer ?',function(choice) {
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

      $('#consultant-settings-edit-form').submit(function(e){
        e.preventDefault();
        $.ajax({
          url: base_url + 'consultants/update-setting',
          dataType: 'json',
          type: 'POST',
          data: {
            max_amount : $(this).find('#form_max_amount').val(),
            date : $(this).find('#form_date_month').val() + '/' + $(this).find('#form_date_day').val() + '/' + $(this).find('#form_date_year').val()
          },
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(ExposeTranslation.get('js:notice', response.message));
              }
            }else {
              window.scrollTo(window.scrollMinX, window.scrollMinY);
              dialoug.slideNotice(response.message);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(ExposeTranslation.get('js:notice'), ExposeTranslation.get('js:an.error.occurred'));
          }
        });
        $(this).val(0);
      });

      $('#coupons a.delete').live('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(ExposeTranslation.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> Gavekortet ?',function(choice) {
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
      $('#coupons-to-customers a.delete').live('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(ExposeTranslation.get('js:notice'), 'Er du sikker på du vil <strong>fjerne</strong> denne bruger ?',function(choice) {
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
      $('#coupon-customer-selector').change(function(){
        selectedOption = $(this).find('option:selected');
        reference = selectedOption.val().split('-');
        customer = reference[0];
        coupon = reference[1];
        $.ajax({
          url: base_url + 'coupons/add-customer',
          dataType: 'json',
          type: 'POST',
          data: {customer : customer, coupon : coupon},
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(ExposeTranslation.get('js:notice', response.message));
              }
            }
            else {
              $('#coupons-to-customers').append('<li><span class="actions"><a href="' + base_url + 'products/delete-category/' + customer + '/' + coupon + '" class="delete" title="Slet">Slet</a></span><span class="name"> ' + selectedOption.text() + '</span></li>');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(ExposeTranslation.get('js:notice'), ExposeTranslation.get('js:an.error.occurred'));
          }
        });
        $(this).val(0);
      });
      // ios class added to body
      switch (navigator.platform) {
        case 'iPad':
        case 'iPhone':
        case 'iPod':
          $('html').addClass('ios');
          break;
      }

      // turn on colorbox for selected elements
      $('a[rel="colorbox"]').each(function (){
        var $element = $(this);
        var iframe = false;
        if ($element.hasClass('iframe')) {
          iframe = true;
        }
        $(this).colorbox({iframe: iframe});
      });

      // preview and re-send orders
      $('a.preview-order, a.resend-order').on('click', function(event) {
        event.preventDefault();
        var oid = $(this).closest('p').find('input').val();
        if (undefined === oid) {
          oid = '';
        } else {
          oid = '/'+oid;
        }
        $.getJSON(this.href+oid, function(result) {
          if (result.status) {
            if (result.message) {
              dialoug.info(ExposeTranslation.get('js:notice'), result.message);
            } else {
              $.colorbox({html: result.data.html});
            }
          } else {
            dialoug.alert(ExposeTranslation.get('js:notice'), result.message);
          }
        })
      });

      // confirm delete...
      $('a[rel="confirm"]').each(function() {
        var $this = $(this);
        var msg = $this.data('confirm-message');
        if (msg) {
          $this.on('click', function(event) {
            event.preventDefault();
            dialoug.confirm('OBS !', msg, function(state) {
              if (state == 'ok') {
                document.location.href = $this.attr('href');
              }
            });
          });
        }
      });
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
