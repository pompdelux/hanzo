var filters = (function ($) {
  'use strict';
  var pub = {};

  pub.init = function() {
    if ($(".js-filters").length === 0) {
      return;
    }

    getValuesFromUrl();

    $(".js-filters span a").click(function(e) {
      e.preventDefault();
      handleFilterRemove($(this).attr('href'));
    });

    var $faceted = $(".js-faceted-form");

    $("input[type='checkbox']", $faceted).on('change', function(event) {

    });

  };

  function handleFilterRemove(value) {
    $(".js-filters span a[href='"+value+"']").parent().remove();
  }

  function addSelectedFilterBox(value) {
    var element = ' <span>'+value+' <a href="'+value+'">&#10005;</a></span>';

    $(".js-filters").append(element);
  }

  function getValuesFromUrl() {
    var $url = $.url();

    if ($url.param('filter') == 'on') {
      $.each($url.param(), function(name, values) {
        if (name == 'filter') {
          return;
        }
        if (typeof values == 'string') {
          $("input[value='"+values+"']", $faceted).prop('checked', true);
        } else if ($.isArray(values)) {
          $.each(values, function(x, value) {
            $("input[value='"+value+"']", $faceted).prop('checked', true);
          });
        }
      });
    }
  }

  return pub;
})(jQuery);
