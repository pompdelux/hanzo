(function($) {
  $.colorbox.settings.fixed = true;
  $.colorbox.settings.width = '97%';
  $.colorbox.settings.maxHeight = '97%';

  $('a.colorbox, a[rel="colorbox"]').colorbox();
  $(document).bind('cbox_load', function(){
    $('#cboxClose').remove();
  });
})(jQuery);

