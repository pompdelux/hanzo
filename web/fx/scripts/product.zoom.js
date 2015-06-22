var product_zoom = (function ($) {
    'use strict';
    var pub = {},
        isMobile = false,
        $productImage = $('.productimage-large a[rel=full-image] img');

    /**
     * Instantiate zoom
     */
    pub.init = function() {
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

        var $large_thumb_link = $('.productimage-large a[rel=full-image]'),
            $large_image_src = $large_thumb_link.attr('href');

        $productImage.data('zoom-image', $large_image_src);

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
        var $large_thumb_link = $('.productimage-large a[rel=full-image]'),
            $small_image_src = $productImage.attr('src'),
            $large_image_src = $large_thumb_link.attr('href');

        // Swap image
        var ez = $productImage.data('elevateZoom');
        ez.swaptheimage($small_image_src, $large_image_src);
    }

    return pub;
})(jQuery);

product_zoom.init();
