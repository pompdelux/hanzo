(function($, document) {
  // rewrite video tags to fit screen, and remove flash fallback
  $('video').each(function(index, element) {
    var $video = $(element);
    $('object', $video).remove();
    $video.attr('width', '100%');
    $video.attr('height', '');
    $video.show();
  });

  var $video = '';
  $('a.video-popper,a.video-embed').on('click', function() {
    event.preventDefault();
    var $this = $(this);

    var $target = $this;
    if ($('.video-container').length) {
      $target = $('.video-container');
    }

    if ($video) {
      var $v = $('.video-js-box', $target);
      $v.slideToggle(400, function() {
        $v.remove();
        $video = '';
      });
      return;
    }

    // load video code and push it into the document
    $target.load(this.href, $this.data(), function() {
      $video = $('video', $target);
      $video.attr('width', '100%');
      $video.attr('height', '');
      $video.slideToggle();
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
})(jQuery, document);
