(function($, document) {
  if ($('body').hasClass('body-frontpage')) {
    var $article = $('.main article'),
        $boxes = $('.grid_4, .grid_2', $article);

    $boxes.sort(function(a, b){
        return $(b).data('order') - $(a).data('order');
    });

    $article.html($boxes);
  }
})(jQuery, document);
