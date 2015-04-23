$(document).ready(function() {

    // menu handeling
    if (false === $('body').hasClass('is-mobile')) {
        var $menu = $('nav.main-menu');

        // set parent li's class to active for active elements.
        $('nav.first.main-menu .active')
            .parents('li')
            .toggleClass('active inactive')
        ;

        var menu_width = 0;
        $('> ul > li > ul > li.heading', $menu).each(function (index, element) {
            var $element = $(element);
            var tmp_width = $element.outerWidth();
            if (menu_width < tmp_width) {
                menu_width = tmp_width;
            }

            $element.addClass('floaded');
        });
        $('> ul > li > ul > li.heading', $menu).closest('ul').each(function (index, element) {
            var $element = $(element);
            var count = $('> li', $element).length;
            $element.css('width', (menu_width * count));
        });

        // Close filters dropdown
        $('nav.filters-dropdowns')
            .find('.outer > li')
            .find('.fa-angle-up')
            .on('click', function() {

                // Run through all containers
                $('nav.filters-dropdowns ').find('ul.outer > li').each(function(key, value) {

                    // Remove open class
                    if($(this).hasClass('open')) {
                        //$(this).removeClass();
                        $(this).removeClass('open');
                    }

                    // Remove on class
                    if($(this).hasClass('on')) {
                        //$(this).removeClass();
                        $(this).removeClass('on');
                    }

                    // $(this).find('> ul').hide();
                });
            });
    }

});