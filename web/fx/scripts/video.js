/**
 * video.js loading
 */
VideoJS.setupAllWhenReady();

$(function() {
  /**
   * attach click events to video "poppers"
   * videos will be opened in a colorbox iframe
   * these will always have autoplay
   *
   * @usage
   * <a href="{{ url('ws_product_video') }}" data-width="480" data-height="270" data-src="ModeshowAW11" data-banner="video_bg" class="video-popper"></a>
   * @params
   * - width...: int
   * - height..: int
   * - src.....: string, name of the video file, without extension and path
   * - banner..: string, default: video_bg, name of the banner file, without extension and path
   */
  $('a.video-popper').click(function(event) {
    event.preventDefault();
    var $this = $(this);
    var height = $this.data('height');
    var width = $this.data('width');

    // call colorbox and play the video.
    $.colorbox({
      iframe : true,
      href: this.href + '?src=' + $this.data('src') + '&height=' + height + '&width=' + width,
      innerHeight: (height + 20) + 'px',
      innerWidth: (width + 15) + 'px',
      className: 'js-keep-close-button',
      close: ''
    });
  });

  /**
   * load videos and embed these in the document where the trigger is.
   *
   * @usage
   * <a href="{{ url('ws_product_video') }}" data-autoplay="0" data-width="480" data-height="270" data-src="ModeshowAW11" data-banner="VB_modeshowAW11" class="video-embed"></a>
   * @params
   * - autoplay: boolean, default: 0
   * - width...: int
   * - height..: int
   * - src.....: string, name of the video file, without extension and path
   * - banner..: string, default: video_bg, name of the banner file, without extension and path
   */
  $('a.video-embed').each(function() {
    var $this = $(this);
    var autoplay = $this.data('autoplay');

    // setup
    var params = $this.data();
    params.embed = 1;

    // load video code and push it into the document
    $this.load(this.href, params, function() {
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
            src = cdn_url+'/video/' +params.src+ '.flv';
          }
          _gaq.push(['_trackPageview', src]);
        }
      }
    });
  });

  // send analytics some data
  $('video').on('play pause ended', function(event) {
    if (_gaq === undefined) {
      return;
    }

    switch(event.type) {
      case 'play':
        _gaq.push(['_trackEvent', 'Videos', 'Play', this.currentSrc]);
        break;
      case 'play':
        _gaq.push(['_trackEvent', 'Videos', 'Paused', this.currentSrc, parseInt(this.getTime(), 10)]);
        break;
      case 'ended':
        _gaq.push(['_trackEvent', 'Videos', 'Ended', this.currentSrc]);
        break;
    }
  });
});
