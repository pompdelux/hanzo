/* jshint unused:false */
var filters = (function ($) {
  'use strict';
  var pub = {};
  var $faceted;

  pub.init = function() {
    if ($(".js-filters").length === 0) {
      return;
    }

    $faceted = $(".js-faceted-form");

    setValuesFromUrl();

    $(".js-filters").on('click', 'span a', function(e) {
      e.preventDefault();
      handleFilterRemove($(this).attr('href'));
      updateUrl();
    });

    $("input[type='checkbox']", $faceted).on('change', function() {
      if ($(this).prop( "checked" ) === true) {
        handleFilterAdded($(this).val(), $(this).data('name'));
      }
      else {
        handleFilterRemove($(this).val());
      }
      updateUrl();
    });

    $(".js-filter-clear-dropdown").click(function(e) {
      e.preventDefault();

      var filterType = $(this).attr('href');
      var selector;
      if ('all' == $(this).attr('href')) {
        selector = "input";
      } else {
        selector = ".js-filter-type-"+filterType+" input";
      }

      $(selector, $faceted).each(function(index, element) {
        handleFilterRemove($(this).val());
      });
      updateUrl();
    });
  };

  /**
   * Updates pager element from listCategoryProducts.html.twig
   */
  function updateUrl() {
    var $a,
        href,
        filter;

    $(".js-pager-container li a").each(function(index, a) {
      $a = $(a);

      if ($a.length) {
        href = $a.attr('href').split('?')[0];
      }

      filter = $faceted.serialize();

      if (filter) {
        href += '?filter=on&'+filter;
      }

      $a.attr('href', href);
      $a.click();
    });
  }

  function handleFilterRemove(value) {
    if ('all' == value) {
      return;
    }

    $(".js-filters span a[href='"+value+"']").parent().remove();
    $("input[value='"+value+"']", $faceted).prop('checked', false);

    if ($(".js-filters span").length == 1) {
      $(".js-filters").addClass('off');
    }
  }

  function handleFilterAdded(value, name) {
    var element = ' <span>'+name+' <a href="'+value+'">&#10005;</a></span>';

    $(".js-filters .last").before(element);
    $("input[value='"+value+"']", $faceted).prop('checked', true);


    if ($(".js-filters span").length > 1) {
      $(".js-filters").removeClass('off');
    }
  }

  function setValuesFromUrl() {
    var $url = $.url(),
        name = '';

    if ($url.param('filter') === 'on') {
      $.each($url.param(), function(name, values) {
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
  }

  return pub;
})(jQuery);
