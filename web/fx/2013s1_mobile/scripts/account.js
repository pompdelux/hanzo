(function($, document) {
    $('#account-block-newsletter-all h3').wrapInner('<span />');
    $('#account-block-newsletter-all h3').on('click', function(event) {
        $('#newsletter-lists-container-all').slideToggle();
        $('#account-block-newsletter-all h3 span').toggleClass('open');
    });
    $('#order-status a.trigger').on('click', function(event) {
        event.preventDefault();
        $('#order-status #'+this.href.split('#')[1]).toggle();
    });

    var $asideSlider = $('aside.js-slider');
    if ($asideSlider.length) {
        $asideSlider.on('click', 'h3', function() {
            $('h3 span', $asideSlider).toggleClass('open');
            $('.container', $asideSlider).first().slideToggle();
        });
    }
})(jQuery, document);
