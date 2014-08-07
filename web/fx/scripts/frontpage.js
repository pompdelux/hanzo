(function($, document) {
  if ($('html').hasClass('touch') === false) {

    // calculate max width
    var expandedWidth = $('#container').width() - ($('nav.main').width() + $('#teasers').width() + 100);
    if (expandedWidth > 670) {
      expandedWidth = 670;
    }

    // calculate height
    $('html').append('<div class="xyz" style="position:absolute; top:-20000px; left: -20000px; width:'+expandedWidth+'px;padding:15px">'+$('#main article').html()+'</div>');
    var expandedHeight = $('html div.xyz div').outerHeight(true);
    $('html div.xyz').remove();

    $('article a.button').data('text', Translator.trans('close'));
    $('article a.button').on('click', function(event) {
      event.preventDefault();

      var $a = $(this);
      var $article = $a.parent();
      var $main = $article.parent();
      var $div = $('div', $article).first();

      // swap lable
      var current_label = $a.text();
      $a.text($a.data('text'));
      $a.data('text', current_label);

      var height;
      var marginTop;
      var maxWidth;

      if ($article.height() > 165) {
        height = 165;
        marginTop = 440;
        maxWidth = 370;
      } else {
        marginTop = 75;
        height = expandedHeight;
        maxWidth = expandedWidth;
      }

      $main.animate({
        marginTop: marginTop,
        width: maxWidth,
        height: height
      });
      $article.animate({
        height: height
      });
      $div.animate({
        height: height - 65
      });
    });
  }
})(jQuery, document);
