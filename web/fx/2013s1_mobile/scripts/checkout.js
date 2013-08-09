(function($, document, undefined) {
    $('#shipping-block h3').on('click', function(event) {
        var $this = $(this);
        $this.next('p').slideToggle(400, function() {
            $this.toggleClass('on');
        });
    });

    $('#in-edit-warning h2').wrapInner('span');
    $('#in-edit-warning h2').on('click', function(event) {
        var $this = $(this);
        $('p', $this.parent()).slideToggle(400, function() {
            $this.toggleClass('on');
        });
    });
})(jQuery, document);
