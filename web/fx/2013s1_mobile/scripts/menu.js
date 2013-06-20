(function($, document, undefined) {
  var $menu = $('nav.main-menu');

  // show menu on subpages.
  $('a.menu-trigger', $menu).on('click', function(event) {
    event.preventDefault();
    $(this).toggleClass('active');
    $('.outer', $menu).slideToggle();
  });

  // set initial "open" classes
  $('li.active', $menu)
    .parent('ul')
    .not('.outer')
    .css('display', 'block')
  ;

  $('a.heading', $menu).on('click', function (event) {
    event.preventDefault();

    var $this = $(this);
    var $li = $this.parent();
    var $ul = $this.next('ul');

    $li.toggleClass('active inactive');

    $ul.slideToggle();
    $ul.toggleClass('open');
    $this.addClass('active');
  });
})(jQuery, document);
