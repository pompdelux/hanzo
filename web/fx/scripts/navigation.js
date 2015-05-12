// This file is mainly used for the purpose if the following screenstates:
// - desktop
// - desktop touch
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

    // Filter toggle click
    $('ul.filter-dropdown a.filter-dropdown-toggle').on('click', function(event) {

        // Don't follow href
        event.preventDefault();

        // Don't track this click (in HTML click)
        event.stopPropagation();

        // Grab parent element (li)
        var $parent = $(this).parent();

        // Is open - close it
        if( $parent.hasClass('open') ) {

            // Close all filters
            close_all_filters();
        }
        // Is closed - open it
        else {

            // Close all filters
            close_all_filters();

            // Add 'open' class
            $parent.addClass('open');
        }
    });

    // Click on filter close icon
    $('ul.filter-dropdown a.js-filter-close').on('click', function(event) {

        // Don't follow href
        event.preventDefault();

        // Close all filters
        close_all_filters();
    });

    // Click inside dropdown menu
    $('ul.filter-dropdown div.filter-dropdown-menu').on('click', function (event) {

        // Don't track this click (in HTML click)
        event.stopPropagation();
    });

    // Click on HTML
    $('html').on('click', function (event) {

        // Close all filters
        close_all_filters();
    });

    // Close all filters
    function close_all_filters() {

        // Configuration
        var $filter = $('ul.filter-dropdown'),
            $list_items = $filter.find('> li.open');

        // Remove 'open' class
        $list_items.removeClass('open');
    }
});