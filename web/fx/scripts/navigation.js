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
        });

        // Click on HTML
        $('html').click(function (event) {
            console.log('CLICK');
            $(navigation, '> ul > li.open').removeClass('open');
        });
    }

    // Filters
    if(Modernizr.touch) {

        // Configuration
        var filter = $('nav.filter-dropdown'),
            filter_container = filter.find('> ul.outer'),
            toggle = filter_container.find('> li');

        // Click on toggle
        toggle.on('click', function() {

            // Toggle 'open' class
            $(this).toggleClass('open');
        });
    }
});