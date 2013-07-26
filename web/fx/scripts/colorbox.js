(function($) {
  $('a.colorbox, a[rel="colorbox"]').colorbox();
  $(document).bind('cbox_load', function(){
    $('#cboxClose').remove();
  });
})(jQuery);
