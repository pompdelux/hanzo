/**
 * @credits http://growmedia.ca/blog/2009/10/14/resizable-full-browser-background-image-with-jquery-preserving-aspect-ratio/
 */

(function($){  
  //Resize image on ready or resize
  $.fn.supersize = function() {
    //Invoke the resizenow() function on document ready
    $(document).ready(function() {
      $('#supersize').resizenow();
    });
    //Invoke the resizenow() function on browser resize
    $(window).bind("resize", function() {
      $('#supersize').resizenow();
    });
  };
  //Adjust image size
  $.fn.resizenow = function() {

    //Define starting width and height values for the original image
    var startwidth = 1270;
    var startheight = 847;
    //Define image ratio
    var ratio = startheight/startwidth;
    //Gather browser dimensions
    var browserwidth = $(window).width();
    //var browserheight = $(window).height();
    var browserheight = $.getDocHeight();
    //Resize image to proper ratio
    if ((browserheight/browserwidth) > ratio) {
      $(this).height(browserheight);
      $(this).width(browserheight / ratio);
      $(this).children().height(browserheight);
      $(this).children().width(browserheight / ratio);
    } else {
      $(this).width(browserwidth);
      $(this).height(browserwidth * ratio);
      $(this).children().width(browserwidth);
      $(this).children().height(browserwidth * ratio);
    }
  };
})(jQuery);
$("div#supersize").supersize();
