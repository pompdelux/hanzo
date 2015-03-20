/* jshint unused:false */
var filters = (function ($) {
  'use strict';
  var pub = {},
      $faceted,
      $selected,
      isMobile = false,
      FORCE_RELOAD = true;

  pub.init = function() {
    if ($(".js-filters").length === 0) {
      return;
    }

    if ($("body.is-mobile").length !== 0) {
      isMobile = true;
    }

    $.cookie.json = true;
    $selected = $(".js-filter-selected-values");
    $faceted = $(".js-faceted-form");

    setSavedValues();
    eventHandlersSetup();
    mobileSetup();
  };

  function showSelectedValues() {
    $selected.show();
  }

  function hideSelectedValues() {
    $selected.hide();
  }

  function eventHandlersSetup() {
    $(".js-filters").on('click', 'span a', function(e) {
      e.preventDefault();
      handleFilterRemove($(this).attr('href'));
      updateSelectedValues();
    });

    $("input[type='checkbox']", $faceted).on('change', function() {
      if ($(this).prop( "checked" ) === true) {
        handleFilterAdded($(this).val(), $(this).data('name'));
      }
      else {
        handleFilterRemove($(this).val());
      }
      updateSelectedValues();
    });

    $(".js-filter-clear-dropdown").click(function(e) {
      e.preventDefault();

      var filterType = $(this).attr('href'),
          selector;

      if ('all' == $(this).attr('href')) {
        selector = "input";
      } else {
        selector = ".js-filter-type-"+filterType+" input";
      }

      $(selector, $faceted).each(function(index, element) {
        handleFilterRemove($(this).val());
      });
      updateSelectedValues(FORCE_RELOAD);
    });
  }

  function mobileSetup() {
    if (!isMobile) {
      return;
    }

    $(".js-filter-open").click(function(e) {
      $(".js-faceted-form").addClass('faceted-form-open');
      $(".js-filter-hide").hide();
      $(".js-filter-show-products").show();
    });
  }

  function updateSelectedValues(reload) {
    if (isMobile) {
      updateUrlMobile(reload);
    }
    else {
      updateUrlDesktop();
    }
    updateCookie();
  }

  function updateCookie() {
    var values = { size: [], color: [], eco: [] },
        group;
    $.each($('input:checked',$faceted), function() {
      group = $(this).data('group');
      values[group].push($(this).val());
    });

    $.cookie('filters-selected-values', values);
  }

  /**
   * Updates pager element from listCategoryProducts.html.twig
   */
  function updateUrlDesktop() {
    var $a;

    $(".js-pager-container li a").each(function(index, a) {
      $a = $(a);
      updateHref($a);
      // Use pager logic in category.js to ajax load products
      $a.click();
    });
  }

  /**
   * On mobile we only perform a page load when the user requests it
   *
   */
  function updateUrlMobile(reload) {
    reload = (typeof reload === "undefined") ? false : reload;

    var $a = $(".js-filter-show-products a");
    updateHref($a);
    if (reload) {
      window.location = $a.attr('href');
    }
  }

  function updateHref($a) {
      var href,
          filter;

      if ($a.length) {
        href = $a.attr('href').split('?')[0];
      }

      filter = $faceted.serialize();

      if (filter) {
        href += '?filter=on&'+filter;
      }

      $a.attr('href', href);
  }

  function handleFilterRemove(value) {
    if ('all' == value) {
      return;
    }

    $(".js-filters span a[href='"+value+"']").parent().remove();
    $("input[value='"+value+"']", $faceted).prop('checked', false);

    if ($(".js-filters span").length == 1) {
      hideSelectedValues();
    }
  }

  function handleFilterAdded(value, name) {
    var element = ' <span>'+name+' <a href="'+value+'">&#10005;</a></span>';

    $(".js-filters .last").before(element);
    $("input[value='"+value+"']", $faceted).prop('checked', true);

    if ($(".js-filters span").length > 1) {
      showSelectedValues();
    }
  }

  function setSavedValues() {
    var $url = $.url(),
        filterCookie = $.cookie('filters-selected-values'),
        refresh = false,
        name = '';

    // Take values from url if any, else from cookie
    if ($url.param('filter') === 'on') {
      $.each($url.param(), function(name, values) {
        // Skip filter parameter
        if (name === 'filter') {
          return;
        }
        if (typeof values === 'string') {
          name = $("input[value='"+values+"']", $faceted).data('name');
          handleFilterAdded(values, name);
        } else if ($.isArray(values)) {
          $.each(values, function(x, value) {
            name = $("input[value='"+value+"']", $faceted).data('name');
            handleFilterAdded(value, name);
          });
        }
      });
    }
    else {
      if (typeof filterCookie != 'undefined') {
        $.each(filterCookie, function(index) {
          if ($.isArray(filterCookie[index]) && filterCookie[index].length > 0) {
            // Currently only do this for size
            if (index != 'size') {
              return true;
            }
            refresh = true;
            $.each(filterCookie[index], function(x, value) {
              name = $("input[value='"+value+"']", $faceted).data('name');
              handleFilterAdded(value, name);
            });
          }
        });
        if (refresh === true) {
          updateSelectedValues();
        }
      }
    }
  }

  return pub;
})(jQuery);
