(function($, document) {
    $('.accordion .pane').hide();
    var accordian_api = $(".accordion").tabs(
      ".accordion div.accordion-pane",
      {
        tabs: '.accordion-trigger',
        effect: 'slide',
        initialIndex: null,
        api: true
      }
    );
})(jQuery, document);
