(function($){
    $('.button.loading').on('click', function() {
      $this = $(this);
      var key = $this.data('info');
      dialoug.loading($this, i18n.t(key));
    });
    $("#body-category-search ul.tabs").tabs("div.panes > div");
})(jQuery);
