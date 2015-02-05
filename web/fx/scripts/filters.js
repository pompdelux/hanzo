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

    $(".js-filters span a").click(function(e) {
      e.preventDefault();
      handleFilterRemove($(this).attr('href'));
    });

    $("input[type='checkbox']", $faceted).on('change', function(event) {
      console.log($(this));
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
    updateUrl();
  }

  function addSelectedFilterBox(value) {
    var element = ' <span>'+value+' <a href="'+value+'">&#10005;</a></span>';

    $(".js-filters").append(element);
    updateUrl();
  }

  function setValuesFromUrl() {
    var $url = $.url();

    if ($url.param('filter') == 'on') {
      $.each($url.param(), function(name, values) {
        if (name == 'filter') {
          return;
        }
        if (typeof values == 'string') {
          // $("input[value='"+values+"']", $faceted).prop('checked', true);
          addSelectedFilterBox(values);
        } else if ($.isArray(values)) {
          $.each(values, function(x, value) {
            addSelectedFilterBox(values);
            // $("input[value='"+value+"']", $faceted).prop('checked', true);
          });
        }
      });
    }
  }

  return pub;
})(jQuery);
