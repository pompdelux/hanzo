(function ($) {
  $.fn.placeFooter();
  $('body').bind('near-you-container.loaded', function() {
    $.fn.placeFooter();
  });
})(jQuery);
