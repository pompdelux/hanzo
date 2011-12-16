/**
 * video.js loading
 */
VideoJS.setupAllWhenReady();

$(function() {
  /**
   * attach click events to video "poppers"
   * videos will be opened in a colorbox iframe
   * these will always have autoplay
   */
  $('a.video-popper').click(function(event) {
    event.preventDefault();
    var $this = $(this);
    var height = $this.data('height');
    var width = $this.data('width');

    // call colorbox and play the video.
    $.colorbox({
      iframe : true,
      href: '/video.php?src=' + $this.data('src') + '&height=' + height + '&width=' + width,
      innerHeight: (height + 20) + 'px',
      innerWidth: (width + 15) + 'px'
    });
  });

  /**
   * load videos and embed these in the document where the trigger is.
   */
  $('a.video-embed').each(function() {
    var $this = $(this);
    var autoplay = $this.data('autoplay');

    // setup
    var params = $this.data();
    params.embed = 1;

    // load video code and push it into the document
    $this.load('/video.php', params, function() {
      var $video = $this.find('video');
      var player = VideoJS.setup($video.attr('id'));

      // remove the <a> tag to prevent "clicks"
      $video.closest('div.video-js-box').unwrap();

      // if autoplay is set, start the video.
      if (autoplay && autoplay == 1) {
        player.play();

        // add tracking to analytics, but only for autoplay
        if (_gaq !== undefined) {
          var src = $video.find('source').first().attr('src');
          if (src === undefined) {
            src = 'http://static.pompdelux.dk/video/' +params.src+ '.flv';
          }
          _gaq.push(['_trackPageview', src]);
        }
      }
    });
  });
});
