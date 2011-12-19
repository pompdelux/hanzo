(function(document, $) {
  // product page
  // tabs (description and washing instructions)
  $("ul.tabs").tabs("div.panes > div");

  // style guides
  $('.productimage-large a').each(function() {
    var $id = $(this).attr('id');
    var $guide = $('.style-guide .'+ $id);
    if ($guide.length) {
      $guide.parent().show();
      $guide.show();
    }
  });

})(document, jQuery);
