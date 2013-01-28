/**
 * Handle stuff for "old ie" browsers.
 * here < 9
 */

(function($, undefined) {
  // design corrections
  var $oldie = $('html.oldie');
  if ($oldie.length) {
    $('footer li:last-child', $oldie).css('background-image', 'none');
    $('#secondary-links ul li:first-child a', $oldie).css('border-left', 'none');
  }
})(jQuery);
