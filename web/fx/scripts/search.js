(function($){
    $('.button.loading').on('click', function() {
      $this = $(this);
      var key = $this.data('info');
      dialoug.loading($this, ExposeTranslation.get('js:'+key));
    });
    $("#body-category-search ul.tabs").tabs("div.panes > div");
})(jQuery);
