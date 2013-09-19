(function($) {
  $('a.colorbox, a[rel="colorbox"]').colorbox();
  $(document).bind('cbox_load', function(){
    if ($('#colorbox.js-keep-close-button').length) {
      $('#cboxClose').addClass('sprite delete');
    } else {
      $('#cboxClose').remove();
    }
  });
})(jQuery);
