/**
 * using the background jquery plugin from:
 * http://vegas.jaysalvat.com/
 *
 * backgrounds are setup in the main template - header
 */
$(function(){
  
  if (vegas_backgrounds != undefined) {
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
