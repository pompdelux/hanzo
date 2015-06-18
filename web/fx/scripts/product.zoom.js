/* jshint unused:false */ // Revealing module pattern
var product_zoom = (function ($) {
    'use strict';
    var pub = {},
        isMobile = false;

    /**
     * Instantiate zoom
     */
    pub.init = function() {

        if ($("body.is-mobile").length !== 0) {
            isMobile = true;
        }

        enableZoom();
    };

    /**
     * Enable zoom
     */
    function enableZoom() {

        if (is_mobile == false) {

            if (! Modernizr.touch) {
                enableDesktopNoTouch;
            }
            else {
                enableDesktopTouch;
            }
        }
        else {
            enableMobile;
        }
    }

    /**
     * Enable on desktop on non-touch devices
     */
    function enableDesktopNoTouch() {}

    /**
     * Enable on desktop on touch devices
     */
    function enableDesktopTouch() {}

    /**
     * Enable on desktop on touch devices
     */
    function enableMobile() {}

    return pub;
})(jQuery);

product_zoom.init();
