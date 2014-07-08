(function($){
    $('.button.loading').on('click', function() {
      var $this = $(this);
      var key = $this.data('info');
      dialoug.loading($this, Translator.trans(''+key));
    });
    $("#body-category-search ul.tabs").tabs("div.panes > div");
})(jQuery);
