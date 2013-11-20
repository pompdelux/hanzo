(function($) {
  var gui = (function($) {
    var pub = {};

    pub.initUI = function() {

      $("a.open-menu").click(function(e) {
        e.preventDefault();
        $(this).parent().find('div').slideToggle();
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

      $('.sortable-item.ui-state-disabled').addClass('collapsed').find(' > ul').hide();
      $('.sortable-item > div.record .record-id').on('click', function(event) {
        event.preventDefault();
        var $li = $(this).closest('li');
        $li.find('ul').first().slideToggle();
        if ($li.hasClass('collapsed')) {
          $li.removeClass('collapsed');
        } else {
          $li.addClass('collapsed');
        }
      });

      /* Admin Sortable list to show and update Cms pages' order */
      $('#sortable-list').nestedSortable({
        listType: 'ul',
        handle: 'div',
        helper: 'clone',
        items: 'li',
        cancel: '',
        opacity: 0.5,
        placeholder: 'helper',
        forcePlaceholderSize: true,
        errorClass: 'err',
        maxLevels: 5,
        tolerance: 'pointer',
        toleranceElement: '> div'
      });

      $('.save-nestedsortable').click(function(e){
        e.preventDefault();
        var list = $('ul#sortable-list').nestedSortable('toArray', {startDepthCount: 0});
        $.ajax({
          url: 'update-tree/',
          dataType: 'json',
          type: 'POST',
          data: {data : list},
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(Translator.get('js:notice'), response.message);
              }
            } else {
              window.scrollTo(window.scrollMinX, window.scrollMinY);
              dialoug.slideNotice(response.message);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
          }
        });
      });

      /* Delete cms node from NestedSortable */
      $('#sortable-list a.delete').click(function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> CMS noden?',function(choice) {
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
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> CMS noden?',function(choice) {
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
      $('#orders a.delete').click(function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> ordren?',function(choice) {
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
      // Add CMS page. Only show children of chosen thread

      $('#form_cms_thread_id').change(function(e){
        var $sel = $('#form_parent_id'),
            thread = $(this).val();
        $('optgroup, optgroup > option', $sel).hide();
        $('optgroup[label^="' + thread + '"]', $sel).children().andSelf().show();
      });


      // CMS edit page, title text field slug creation
      $('#cms-edit-form #form_title').blur(function(e){
        if($('#form_path').val().length === 0 && $('#form_title').val().length !== 0){
          var slug = '';
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
          var title = $('#form_title').val();

          for (var i = 0; i < title.length; i++) {
              if ( chars[title.charAt(i)] ) {
                slug += chars[title.charAt(i)];
              } else {
                slug += title.charAt(i);
              }
          }
          slug = slug.replace(/[^-\w\s$\*\(\)\'\!\_]/g, '-');  // remove unneeded chars
          slug = slug.replace(/^\s+|\s+$/g, ''); // trim leading/trailing spaces
          slug = slug.replace(/[-\s]+/g, '-');   // convert spaces
          slug = path + '/' + slug.replace(/-$/, '');         // remove trailing separator
          slug = slug.toLowerCase();
          $('#form_path').val(slug);
        }
      });

      // Sortable list for products ind a category
      $('ul#product-list-sort').sortable({
        axis : 'y',
        distance : 30,
        forceHelperSize : true,
        forcePlaceholderSize : true,
        items : 'li',
        opacity : 0.5,
        placeholder : 'placeholder',
        scroll: true
      });
      $('.save-sortable').click(function(e){
        e.preventDefault();
        var list = $('ul#product-list-sort').sortable('toArray');
        $.ajax({
          url: '../../update-sort/',
          dataType: 'json',
          type: 'POST',
          data: {data : list},
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(Translator.get('js:notice', response.message));
              }
            }
            else {
              window.scrollTo(window.scrollMinX, window.scrollMinY);
              dialoug.slideNotice(response.message);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
          }
        });
      });

      //ProductsToImages on Products page
      $('.product-selector').change(function(){
        var $product_select = $(this);
        var selectedOption = $product_select.find('option:selected');
        var $color_select = $product_select.next('.product-color-selector');
        var reference = selectedOption.val().split('-');
        var image = reference[0];
        var product = reference[1];
        $.ajax({
          url: '../reference-get-color/',
          dataType: 'json',
          type: 'POST',
          data: {image : image, product : product},
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(Translator.get('js:notice', response.message));
              }
            }
            else {
              $color_select.find('option')
                .not('.initial')
                .remove();
              if(response.data.length > 0) {
                $.each(response.data, function(key, value) {
                  $color_select
                    .append($("<option></option>")
                    .attr("value",value)
                    .text(value));
                });
                $color_select.removeAttr('disabled');
              }
              else {
                $color_select.attr('disabled', 'disabled');
                $color_select.val(0);
              }
              // $('#item-' + image + ' .product-references').append('<li><span class="actions"><a href="' + base_url + 'products/delete-reference/' + image + '/' + product + '" class="delete" title="Slet">Slet</a></span><span class="id">(#' + product + ')</span><span class="sku">' + selectedOption.text() + '</span></li>');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
          }
        });
      });

      //ProductsToImages on Products page
      $('.product-color-selector').change(function(){
        var $color_select = $(this);
        var $product_select = $color_select.prev('.product-selector');
        var selectedOption = $product_select.find('option:selected');
        var reference = selectedOption.val().split('-');
        var image = reference[0];
        var product = reference[1];
        var color = $color_select.find('option:selected').val();
        $.ajax({
          url: '../add-reference/',
          dataType: 'json',
          type: 'POST',
          data: {image : image, product : product, color : color},
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(Translator.get('js:notice', response.message));
              }
            }
            else {
              $('#item-' + image + ' .product-references').append('<li><span class="id">(#' + product + ')</span><span class="sku">' + selectedOption.text() + '</span> - <span class="color">' + color + '</span></li>');
              $color_select.attr('disabled', 'disabled')
                .find('option')
                .not('.initial')
                .remove();
              $product_select.val(0);
              $color_select.val(0);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
          }
        });
      });


      $('#product-images-list a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> Billede referencen?',function(choice) {
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

      //ProductsImagesCategoriesSort on Products page
      $('.image-category-selector').change(function(){
        var selectedOption = $(this).find('option:selected');
        var reference = selectedOption.val().split('-');
        var image = reference[0];
        var category = reference[1];
        $.ajax({
          url: '../add-image-to-category/',
          dataType: 'json',
          type: 'POST',
          data: {image : image, category : category},
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(Translator.get('js:notice', response.message));
              }
            }
            else {
              $('#item-' + image + ' .image-categories').append('<li><span class="actions"><a href="' + base_url + 'products/delete-image-from-category/' + image + '/' + category + '" class="delete" title="Slet">Slet</a></span><span class="sku">' + selectedOption.text() + '</span></li>');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
          }
        });
        $(this).val(0);
      });

      // CategoriesToProducts on Products page
      $('#product-category-selector').change(function(){
        var selectedOption = $(this).find('option:selected');
        var reference = selectedOption.val().split('-');
        var category = reference[0];
        var product = reference[1];
        $.ajax({
          url: '../add-category/',
          dataType: 'json',
          type: 'POST',
          data: {category : category, product : product},
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(Translator.get('js:notice', response.message));
              }
            }
            else {
              $('#product-categories').append('<li><span class="actions"><a href="' + base_url + 'products/delete-category/' + category + '/' + product + '" class="delete" title="Slet">Slet</a></span><span class="id">(#' + category + ')</span><span class="title"> ' + selectedOption.text() + '</span></li>');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
          }
        });
        $(this).val(0);
      });

      $('#product-categories a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> Kategorien fra produktet ?',function(choice) {
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

      // Related Products on Products page
      $('#product-related-products-selector').change(function(){
        var selectedOption = $(this).find('option:selected');
        var reference = selectedOption.val().split('-');
        var master = reference[0];
        var sku = reference[1];
        $.ajax({
          url: '../add-related/',
          dataType: 'json',
          type: 'POST',
          data: {master : master, sku : sku},
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(Translator.get('js:notice', response.message));
              }
            }
            else {
              $('#product-related-products').append('<li><span class="actions"><a href="' + base_url + 'products/delete-related/' + master + '/' + sku + '" class="delete" title="Slet">Slet</a></span><span class="title"> ' + selectedOption.text() + '</span></li>');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
          }
        });
        $(this).val(0);
      });

      $('#product-related-products a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> produktet fra realterede produkter ?',function(choice) {
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

      $('#translation-list a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> oversættelsen til kategorien ?',function(choice) {
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

      $('#category-list a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> denne kategori ?',function(choice) {
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

      $('#washing a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> denne kategori ?', function(choice) {
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

      $('#messages a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> denne besked ?',function(choice) {
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

      $('#redirects a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> denne Redirect ?', function(choice) {
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

      $('#zip_to_city a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> dette Post Nummer ?',function(choice) {
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

      $('#customers a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> denne kunde ?',function(choice) {
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
                dialoug.alert(Translator.get('js:notice', response.message));
              }
            }else {
              window.scrollTo(window.scrollMinX, window.scrollMinY);
              dialoug.slideNotice(response.message);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
          }
        });
        $(this).val(0);
      });

      $('#gift-cards a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>slette</strong> Gavekortet ?',function(choice) {
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
      $('#gift-cards-to-customers a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>fjerne</strong> denne bruger ?',function(choice) {
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
      $('#gift-card-customer-selector').change(function(){
        var selectedOption = $(this).find('option:selected');
        var reference = selectedOption.val().split('-');
        var customer = reference[0];
        var gift_card = reference[1];
        $.ajax({
          url: base_url + 'gift-cards/add-customer',
          dataType: 'json',
          type: 'POST',
          data: {customer : customer, gift_card : gift_card},
          async: false,
          success: function(response, textStatus, jqXHR) {
            if (false === response.status) {
              if (response.message) {
                dialoug.alert(Translator.get('js:notice', response.message));
              }
            }
            else {
              $('#gift-cards-to-customers').append('<li><span class="actions"><a href="' + base_url + 'products/delete-category/' + customer + '/' + gift_card + '" class="delete" title="Slet">Slet</a></span><span class="name"> ' + selectedOption.text() + '</span></li>');
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            dialoug.error(Translator.get('js:notice'), Translator.get('js:an.error.occurred'));
          }
        });
        $(this).val(0);
      });
      $('#helpdesk a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>fjerne</strong> denne ?',function(choice) {
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
      /* Products View */
      $('#styles a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>fjerne</strong> denne style?',function(choice) {
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
      $('#quantity_discounts a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>fjerne</strong> denne rabat?',function(choice) {
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
      /* Settings Languages */
      $('#languages a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>fjerne</strong> dette sprog?',function(choice) {
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
      /* Delete event */
      $('#events a.delete').on('click',function(e){
        e.preventDefault();
        var $a = $(this);
        dialoug.confirm(Translator.get('js:notice'), 'Er du sikker på du vil <strong>fjerne</strong> dette event?',function(choice) {
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

      // turn on colorbox for selected elements
      $('a[rel="colorbox"]').each(function (){
        var $element = $(this);
        var iframe = false;
        if ($element.hasClass('iframe')) {
          iframe = true;
        }
        $(this).colorbox({iframe: iframe});
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
    };

    /**
     * animated effect on first menu level
     */
    pub.initAnimatedMenu = function() {
      $('nav.main > ul > li > a').hover(function() {
        var $this = $(this);
        var left = $this.data('pl-save');
        if (left === undefined) {
          left = parseInt($this.css('padding-left'), 0);
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
    };



    return pub;
  })(jQuery);

  gui.initUI();
  gui.initAnimatedMenu();

})(document, jQuery);

function helpdesk_open (key) {
  var pptable = prettyPrint($.parseJSON(key), { maxDepth : 6 });
  var defaults = {
    'close' : Translator.get('js:close'),
    'overlayClose' : true,
    'escKey' : true,
    'html' : '<div class="dialoug alert info"><h2>' + Translator.get('js:notice') + '</h2>' + pptable.innerHTML + '</div>'
  };
  $.colorbox(defaults);
  return false;
}
