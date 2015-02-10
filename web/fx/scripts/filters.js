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
        handleFilterAdded($(this).val());
      }
      else {
        handleFilterRemove($(this).val());
      }
      updateUrl();
    });

    $(".js-filter-clear-dropdown").click(function(e) {
      e.preventDefault();
      var filterType = $(this).attr('href');
      $(".js-filter-type-"+filterType+" input").each(function(index, element) {
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
    $(".js-filters span a[href='"+value+"']").parent().remove();
    $("input[value='"+value+"']", $faceted).prop('checked', false);
  }

  function handleFilterAdded(value) {
    var name = value.replace('token-', '');

    var element = ' <span>'+name+' <a href="'+value+'">&#10005;</a></span>';

    $(".js-filters").append(element);
    $("input[value='"+value+"']", $faceted).prop('checked', true);
  }

  function setValuesFromUrl() {
    var $url = $.url();

    if ($url.param('filter') === 'on') {
      $.each($url.param(), function(name, values) {
        if (name === 'filter') {
          return;
        }
        if (typeof values === 'string') {
          handleFilterAdded(values);
        } else if ($.isArray(values)) {
          $.each(values, function(x, value) {
            handleFilterAdded(value);
          });
        }
      });
    }

    updateUrl();
  }

  return pub;
})(jQuery);
