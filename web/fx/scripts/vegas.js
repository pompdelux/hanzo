/**
 * using the background jquery plugin from:
 * http://vegas.jaysalvat.com/
 *
 * backgrounds are setup in the main template - header
 */
$(function(){
  var vegas_backgrounds;
  if ($('body#body-frontpage').length && $('html').hasClass('touch') === false) {
    // frontpage and not mobile
    vegas_backgrounds = [
      { src: cdn_url+'/fx/images/bg/01.jpg', fade:1000, loading: false, valign: 'top' },
      { src: cdn_url+'/fx/images/bg/02.jpg', fade:1000, loading: false, valign: 'top' },
      { src: cdn_url+'/fx/images/bg/03.jpg', fade:1000, loading: false, valign: 'top' },
      { src: cdn_url+'/fx/images/bg/04.jpg', fade:1000, loading: false, valign: 'top' },
      { src: cdn_url+'/fx/images/bg/05.jpg', fade:1000, loading: false, valign: 'top' },
      { src: cdn_url+'/fx/images/bg/06.jpg', fade:1000, loading: false, valign: 'top' }
    ];
  } else {
    vegas_backgrounds = [
      { src: cdn_url+'/fx/images/bg/bg_pdl.jpg', valign: 'top', loading: false }
    ];
  }

  if (vegas_backgrounds) {
    if (vegas_backgrounds.length > 1) {
      $.vegas('slideshow', {
        preload : true,
        backgrounds : vegas_backgrounds
      });
    } else {
      $.vegas(vegas_backgrounds[0]);
    }
  }


// if you want overlays - use it like this.
//
//  $.vegas('slideshow', {
//    preload : true,
//    backgrounds : vegas_backgrounds
//  })('overlay', {
//    src:'/templates/pompdelux/scripts/vegas/overlays/01.png'
//  });
});
