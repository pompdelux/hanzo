(function($) {

  /** Don't show the bubble if click dismiss button at 3 times. */
  google.bookmarkbubble.Bubble.prototype.NUMBER_OF_TIMES_TO_DISMISS=1;

  google.bookmarkbubble.Bubble.prototype.REL_ICON_ = 'apple-touch-icon-precomposed';

  /**
   * Popup message to create shortcut to Home.
   */
  google.bookmarkbubble.Bubble.prototype.msg = {
    android: Translator.get('js:bookmarkbubble.android') + '<div class="clear" style="clear:both;"></div>',
    android3: Translator.get('js:bookmarkbubble.android3', { "image_url" : google.bookmarkbubble.Bubble.prototype.IMAGE_ANDROID3_BOOKMARK_DATA_URL_ }) + '<div class="clear" style="clear:both;"></div>',
    android4: Translator.get('js:bookmarkbubble.android4', { "image_url" : google.bookmarkbubble.Bubble.prototype.IMAGE_ANDROID4_MOBILE_BOOKMARK_DATA_URL_ }) + '<div class="clear" style="clear:both;"></div>',
    blackberry: Translator.get('js:bookmarkbubble.blackberry', { "image_url" : google.bookmarkbubble.Bubble.prototype.IMAGE_BLACKBERRY_ICON_DATA_URL_ }) + '<div class="clear" style="clear:both;"></div>',
    playbook: Translator.get('js:bookmarkbubble.playbook', { "image_url" : google.bookmarkbubble.Bubble.prototype.IMAGE_PLAYBOOK_BOOKMARK_DATA_URL_ }) + '<div class="clear" style="clear:both;"></div>',
    ios42orlater : Translator.get('js:bookmarkbubble.ios42orlater', { "image_url" : google.bookmarkbubble.Bubble.prototype.IMAGE_SAFARI_FORWARD_DATA_URL_ }) + '<div class="clear" style="clear:both;"></div>',
    ioslegacy: Translator.get('js:bookmarkbubble.ioslegacy') + '<div class="clear" style="clear:both;"></div>'
  };

  /** page to bookmark bubble (generally, this should be top page) */
  if(typeof(page_popup_bubble)=="undefined"){
    page_popup_bubble = "body";
  }

  $(document).ready(function() {
    window.setTimeout(function() {
      var bubble = new google.bookmarkbubble.Bubble();

      var parameter = page_popup_bubble;

      bubble.hasHashParameter = function() {
        return false;
      };

      bubble.setHashParameter = function() {
        return true;
      };

      bubble.showIfAllowed();
    }, 1000 /** delay to show the bubble */ );
  });

})(jQuery);
