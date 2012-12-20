(function($) {
    $(window).on('load', function() {
        var preload = [];
        $("img.hover").each(function() {
            if($(this).data('hover')){
                preload.push($(this).data('hover'));
            }
        });
        var img = document.createElement('img');
        $(img).on('load', function() {
            if(preload[0]) {
                this.src = preload.shift();
            }
        }).trigger('load');
    });
})(jQuery);
