/* jshint strict:false */
/* global ga:true*/
$(document).ready(function(){
  if ( $('body.body-frontpage').length > 0 ) {
    // Track clicks on homepage banners
    $('div#main a').click(function(e) {
      ga('send', 'event', 'Homepage Banner Click', this.href.split('?')[0], this.childNodes[0].getAttribute('src').split('?')[0]);
      if (this.target != '_blank') {
        e.preventDefault();
        var url = this.href;
        setTimeout(function() { location.href = url; }, 150);
      }
    });
  }

  if ( $('body.body-category').length > 0 ) {
    // Track clicks on submenu link items
    $('div.sub-menu a').click(function(e) {
      if(this.href.indexOf('#') == -1) {
        ga('send', 'event', 'Submenu Navigation Click', this.href.split('/')[4], 'Link: ' + this.innerHTML);
      }
      if(this.target != '_blank') {
        e.preventDefault();
        var url = this.href;
        setTimeout(function() { location.href = url; }, 150);
      }
    });

    // Track clicks on submenu filter items
    $('div.sub-menu input').click(function() {
      ga('send', 'event', 'Submenu Filter Click', document.location.href.split('/')[4], 'Filter: ' + this.getAttribute('name'));
    });
  }

  // Track clicks on main navigation menu items
  $('nav.main-menu a').click(function(e) {
    if(this.href.indexOf('#') == -1) {
      ga('send', 'event', 'Main Navigation Click', this.href.split('/')[4], this.innerHTML);
    }
    if(this.target != '_blank') {
      e.preventDefault();
      var url = this.href;
      setTimeout(function() { location.href = url; }, 150);
    }
  });
});
