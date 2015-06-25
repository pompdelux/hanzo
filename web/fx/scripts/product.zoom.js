var productZoom = (function ($) {
    'use strict';
    var pub = {},
        isMobile = false,
        $productImage = null;

    /**
     * Instantiate zoom
     */
    pub.init = function() {
        $productImage = $('.productimage-large a[rel=full-image] img');

        if ($('body').hasClass('is_mobile')) {
            isMobile = true;
        }
        if ($productImage.length === 0) {
            return;
        }

        eventListener();
        enableZoom();
    };

    /**
     * Event listener
     */
    function eventListener() {
        $(document).on('product.image.change', function() {
            updateZoomImage();
        });
    }

    /**
     * Enable zoom
     */
    function enableZoom() {
        if (isMobile == true || Modernizr.touch) {
            return;
        }

        var $largeThumbLink = $('.productimage-large a[rel=full-image]'),
            $largeImageSrc = $largeThumbLink.attr('href');

        $productImage.data('zoom-image', $largeImageSrc);

        // Enable zoom
        $productImage.elevateZoom({
            zoomWindowOffetx: 184,
            zoomWindowOffety: 6,
            zoomWindowWidth: 414,
            borderSize: 2
        });
    }

    /**
     * Update zoom image
     */
    function updateZoomImage() {
        var $largeThumbLink = $('.productimage-large a[rel=full-image]'),
            $smallImageSrc = $productImage.attr('src'),
            $largeImageSrc = $largeThumbLink.attr('href');

        // Swap image
        var ez = $productImage.data('elevateZoom');
        ez.swaptheimage($smallImageSrc, $largeImageSrc);
    }

    return pub;
})(jQuery);

productZoom.init();
