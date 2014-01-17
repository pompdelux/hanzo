(function($, document, undefined) {
  var $menu = $('nav.main-menu');

  // show menu on subpages.
  $('a.menu-trigger', $menu).on('click', function(event) {
    event.preventDefault();
    $(this).toggleClass('active');
    $('.outer', $menu).slideToggle();
  });

  // set initial "open" classes on all parent ULs
  $('li.active', $menu)
    .parents('ul.inner')
    .css('display', 'block')
    .addClass('open');

  $('ul.topmenu ul ul', $menu)
    .css('display', 'block')
    .addClass('open');

  $('ul a', $menu).on('click', function (event) {

    var $this = $(this);
    var $li = $this.parent();
    var $ul = $this.next('ul');

    if ($ul.length && $ul.hasClass('open') === false || !$this.attr('href').length || $this.hasClass('heading')) {
      if ((!navigator.userAgent.match(/iPhone/i)) && (!navigator.userAgent.match(/iPod/i)) && (!navigator.userAgent.match(/iPad/i))) {
        event.preventDefault();

        $li.toggleClass('active inactive');

        $ul.slideToggle();
        $ul.toggleClass('open');
        $this.addClass('active');

      }
    }

  });
})(jQuery, document);
