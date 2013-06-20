(function($) {
    $(window).on('load', function() {
        var preload = [];
        $("img.flip").each(function() {
            if($(this).data('flip')){
                preload.push($(this).data('flip'));
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
