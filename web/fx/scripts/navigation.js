$(document).ready(function() {

    // Main navigation
    if(Modernizr.touch) {

        // Configuration
        var navigation = $('nav.navigation-main'),
            navigation_container = navigation.find('> ul.outer'),
            toggle = navigation_container.find('> li > a');

        // Click on toggle
        toggle.on('click', function(event) {

            // Configuration
            var parent = $(this).parent(),
                is_open = parent.hasClass('open'),
                dropdown_menu = $(this).parent().find('> ul');

            // Has dropdown menu
            if(dropdown_menu.length > 0) {

                // Remove 'open' class from all
                $('nav.navigation-main > ul > li.open').removeClass('open');

                // Currently closed - open dropdown
                if(is_open === false) {

                    // Add 'open' class
                    parent.addClass('open');
                }

                // Don't track
                event.stopPropagation();

                // Don't follow link
                event.preventDefault();
            }

            // Add click event to dropdown menu, so that we are sure that a click on the background won't close it
            dropdown_menu.on('click', function(event) {

                // Don't track
                event.stopPropagation();

            });
        });

        // Click on HTML
        $('html').on('click', function (event) {
            $('> ul > li.open', navigation).removeClass('open');
        });

        // Add menu close icon
        var $close_icon = $('<i />').addClass('fa').addClass('fa-angle-up'),
            $list_item = $('<li />').addClass('js-menu-close').addClass('menu-close').append($close_icon);
        navigation_container.find('> li > ul').append($list_item);

        // Click on close
        navigation.find('.js-menu-close').on('click', function(event) {
            $('> ul > li.open', navigation).removeClass('open');
        });
    }

    // Filters

    // Configuration
    var $filter = $('nav.filters-dropdowns'),
        $link_containers = $filter.find('ul.outer > li'),
        $links = $link_containers.find('> div > a'),
        $dropdown_menus = $link_containers.find('> div > ul');

    // Click on link
    $links.on('click', function(event) {

        // Don't follow href
        event.preventDefault();

    });

    // Touch devices
    if(Modernizr.touch) {

        // Click on link
        $links.on('click', function(event) {

            // Don't track this click (in HTML click)
            event.stopPropagation();

            var $container = $(this).parent().parent();

            // Open
            if($container.hasClass('open')) {

                // Remove open class
                $container.removeClass('open');

            }

            // Closed - open it
            else {

                // Remove open class from all other
                $link_containers.removeClass('open');

                // Add open class
                $container.addClass('open');

            }

        });

        // Click inside dropdown menus
        $dropdown_menus.on('click', function(event) {

            // Don't track this click (in HTML click)
            event.stopPropagation();

        });

        // Click on close icon
        $filter.find('.js-menu-close').on('click', function(event) {

            // Remove open class from all other
            $link_containers.removeClass('open');

        });

        // Click on HTML
        $('html').on('click', function (event) {
            $link_containers.removeClass('open');
        });
    }
});