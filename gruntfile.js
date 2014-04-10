module.exports = function (grunt) {
    "use strict";

    var files = {
        scss : [
            'web/fx/2013s1/sass/*.scss',
            'web/fx/2013s1_mobile/sass/*.scss'
        ],

        desktop_css : [
            'web/fx/2013s1/css/960_8_3_3.css',
            'web/fx/2013s1/css/jquery.fullimagebox.css',
            'web/fx/css/colorbox/theme4/colorbox.css',
            'web/fx/css/jquery-ui.css',
            'web/fx/scripts/video-js/video-js.css',
            'web/fx/scripts/fullcalendar/fullcalendar.css',
            'web/fx/2013s1/css/screen.css',
            'web/fx/2013s1/css/style.css'
        ],

        mobile_css: [
            'web/fx/css/jquery-ui.css',
            'web/fx/scripts/fullcalendar/fullcalendar.css',
            'web/fx/css/colorbox/theme4/colorbox.css',
            'web/fx/2013s1_mobile/css/mobile.css'
        ],

        desktop_js: [
            'web/fx/scripts/jquery.min.js',
            'web/fx/scripts/json2.js',
            'web/fx/scripts/jquerytools/src/tabs/tabs.js',
            'web/fx/scripts/jquerytools/src/tabs/tabs.slideshow.js',
            'web/fx/scripts/jquerytools/src/overlay/overlay.js',
            'web/fx/scripts/jquerytools/src/scrollable/scrollable.js',
            'web/fx/scripts/jquerytools/src/scrollable/scrollable.autoscroll.js',
            'web/fx/scripts/jquerytools/src/scrollable/scrollable.navigator.js',
            'web/fx/scripts/tinymce/jscripts/tiny_mce/jquery.tinymce.js',
            'web/fx/scripts/colorbox/jquery.colorbox-min.js',
            'web/fx/scripts/zoom/jquery.zoom.js',
            'web/fx/scripts/accounting/accounting.min.js',
            'web/fx/scripts/jquery.formparams.js',
            'web/fx/scripts/jquery.cycle2.min.js',
            'web/fx/scripts/jquery.fullimagebox.js',
            'web/fx/scripts/jquery-cookie/jquery.cookie.js',
            'web/fx/scripts/jquery.nanoscroller.min.js',
            'web/fx/scripts/flowplayer/flowplayer-3.2.11.min.js',
            'web/fx/scripts/countdown/jquery.countdown.min.js',
            'web/fx/scripts/countdown/jquery.countdown-da.js',
            'web/fx/scripts/countdown/jquery.countdown-de.js',
            'web/fx/scripts/countdown/jquery.countdown-fi.js',
            'web/fx/scripts/countdown/jquery.countdown-nb.js',
            'web/fx/scripts/countdown/jquery.countdown-nl.js',
            'web/fx/scripts/countdown/jquery.countdown-sv.js',
            'web/fx/scripts/video-js/video.js',
            'web/fx/scripts/history.js/scripts/bundled/html4+html5/jquery.history.js',
            'web/fx/scripts/purl/purl.js',
            'web/fx/scripts/jaiks/jaiks.min.js',
            'web/fx/scripts/yatzy.js',
            'web/fx/scripts/dialoug.js',
            'web/fx/scripts/colorbox.js',
            'web/fx/scripts/flowplayer.js',
            'web/fx/scripts/video.js',
            'web/fx/scripts/main.js',
            'web/fx/scripts/social-buttons.js',
            'web/fx/scripts/category.js',
            'web/fx/scripts/product.js',
            'web/fx/scripts/cart.js',
            'web/fx/scripts/account.js',
            'web/fx/scripts/preload.js',
            'web/fx/scripts/checkout.js',
            'web/fx/scripts/dibs-process.js',
            'web/fx/scripts/wall.js',
            'web/fx/scripts/events.js',
            'web/fx/scripts/quickorder.js',
            'web/fx/scripts/newsletter.js',
            'web/fx/scripts/gothia.js',
            'web/fx/scripts/maps.js',
            'web/fx/scripts/search.js',
            'web/fx/scripts/mannequin.js',
            'web/fx/scripts/faq.js',
            'web/fx/scripts/rma.js',
            'web/fx/scripts/jquery.placeholder-enhanced.min.js',
            'web/fx/scripts/oldie.js'
        ],

        mobile_js: [
            'web/fx/scripts/jquery.min.js',
            'web/fx/scripts/json2.js',
            'web/fx/scripts/jquerytools/src/tabs/tabs.js',
            'web/fx/scripts/jquerytools/src/tabs/tabs.slideshow.js',
            'web/fx/scripts/jquerytools/src/overlay/overlay.js',
            'web/fx/scripts/jquerytools/src/scrollable/scrollable.js',
            'web/fx/scripts/jquerytools/src/scrollable/scrollable.autoscroll.js',
            'web/fx/scripts/jquerytools/src/scrollable/scrollable.navigator.js',
            'web/fx/scripts/jquery.cycle2.min.js',
            'web/fx/scripts/colorbox/jquery.colorbox-min.js',
            'web/fx/scripts/jaiks/jaiks.min.js',
            'web/fx/scripts/accounting/accounting.min.js',
            'web/fx/scripts/jquery.formparams.js',
            'web/fx/scripts/countdown/jquery.countdown.js',
            'web/fx/scripts/countdown/jquery.countdown-da.js',
            'web/fx/scripts/countdown/jquery.countdown-de.js',
            'web/fx/scripts/countdown/jquery.countdown-nb.js',
            'web/fx/scripts/countdown/jquery.countdown-sv.js',
            'web/fx/scripts/countdown/jquery.countdown-fi.js',
            'web/fx/scripts/countdown/jquery.countdown-nl.js',
            'web/fx/scripts/jquery-cookie/jquery.cookie.js',
            'web/fx/scripts/jquery.nanoscroller.min.js',
            'web/fx/scripts/dialoug.js',
            'web/fx/scripts/yatzy.js',
            'web/fx/scripts/main.js',
            'web/fx/scripts/cart.js',
            'web/fx/scripts/checkout.js',
            'web/fx/scripts/dibs-process.js',
            'web/fx/scripts/gothia.js',
            'web/fx/scripts/events.js',
            'web/fx/scripts/maps.js',
            'web/fx/scripts/faq.js',
            'web/fx/2013s1_mobile/scripts/frontpage.js',
            'web/fx/2013s1_mobile/scripts/colorbox.js',
            'web/fx/2013s1_mobile/scripts/jquery.hammer.js',
            'web/fx/2013s1_mobile/scripts/menu.js',
            'web/fx/2013s1_mobile/scripts/account.js',
            'web/fx/2013s1_mobile/scripts/video.js',
            'web/fx/2013s1_mobile/scripts/product.js',
            'web/fx/2013s1_mobile/scripts/accordion.js',
            'web/fx/scripts/account.js',
            'web/fx/scripts/newsletter.js',
            'web/fx/scripts/events.js',
            'web/fx/2013s1_mobile/scripts/checkout.js'
        ],

        admin_js: [
            'web/fx/scripts/jquery.tools.min.js',
            'web/fx/scripts/jquery.ui.nestedSortable.js',
            'web/fx/scripts/colorbox/jquery.colorbox-min.js',
            'web/fx/scripts/zoom/jquery.zoom.js',
            'web/fx/scripts/flowplayer/flowplayer-3.2.11.min.js',
            'web/fx/scripts/countdown/jquery.countdown.js',
            'web/fx/scripts/countdown/jquery.countdown-dk.js',
            'web/fx/scripts/video-js/video.js',
            'web/fx/scripts/history.js/scripts/bundled/html4+html5/jquery.history.js',
            'web/fx/scripts/yatzy.js',
            'web/fx/scripts/dialoug.js',
            'web/fx/scripts/colorbox.js',
            'web/fx/scripts/flowplayer.js',
            'web/fx/scripts/video.js',
            'web/fx/scripts/admin.js',
            'web/fx/scripts/admin.gothia.js',
            'web/fx/scripts/maps.js',
            'web/fx/scripts/admin.dead_orders.js'
        ],

        consultant_js: [
            'web/fx/scripts/jquery.min.js',
            'web/fx/scripts/json2.js',
            'web/fx/scripts/jquerytools/src/tabs/tabs.js',
            'web/fx/scripts/jquerytools/src/tabs/tabs.slideshow.js',
            'web/fx/scripts/jquerytools/src/overlay/overlay.js',
            'web/fx/scripts/jquerytools/src/scrollable/scrollable.js',
            'web/fx/scripts/jquerytools/src/scrollable/scrollable.autoscroll.js',
            'web/fx/scripts/jquerytools/src/scrollable/scrollable.navigator.js',
            'web/fx/scripts/tinymce/jscripts/tiny_mce/jquery.tinymce.js',
            'web/fx/scripts/colorbox/jquery.colorbox-min.js',
            'web/fx/scripts/zoom/jquery.zoom.js',
            'web/fx/scripts/accounting/accounting.min.js',
            'web/fx/scripts/jquery.formparams.js',
            'web/fx/scripts/jquery.cycle2.min.js',
            'web/fx/scripts/jquery.fullimagebox.js',
            'web/fx/scripts/jquery-cookie/jquery.cookie.js',
            'web/fx/scripts/jquery.nanoscroller.min.js',
            'web/fx/scripts/flowplayer/flowplayer-3.2.11.min.js',
            'web/fx/scripts/countdown/jquery.countdown.min.js',
            'web/fx/scripts/countdown/jquery.countdown-da.js',
            'web/fx/scripts/countdown/jquery.countdown-de.js',
            'web/fx/scripts/countdown/jquery.countdown-nb.js',
            'web/fx/scripts/countdown/jquery.countdown-sv.js',
            'web/fx/scripts/countdown/jquery.countdown-fi.js',
            'web/fx/scripts/countdown/jquery.countdown-nl.js',
            'web/fx/scripts/video-js/video.js',
            'web/fx/scripts/history.js/scripts/bundled/html4+html5/jquery.history.js',
            'web/fx/scripts/jaiks/jaiks.min.js',
            'web/fx/scripts/yatzy.js',
            'web/fx/scripts/dialoug.js',
            'web/fx/scripts/colorbox.js',
            'web/fx/scripts/flowplayer.js',
            'web/fx/scripts/video.js',
            'web/fx/scripts/main.js',
            'web/fx/scripts/social-buttons.js',
            'web/fx/scripts/category.js',
            'web/fx/scripts/product.js',
            'web/fx/scripts/cart.js',
            'web/fx/scripts/account.js',
            'web/fx/scripts/preload.js',
            'web/fx/scripts/checkout.js',
            'web/fx/scripts/dibs-process.js',
            'web/fx/scripts/wall.js',
            'web/fx/scripts/events.js',
            'web/fx/scripts/quickorder.js',
            'web/fx/scripts/newsletter.js',
            'web/fx/scripts/consultant.newsletter.js',
            'web/fx/scripts/gothia.js',
            'web/fx/scripts/maps.js',
            'web/fx/scripts/search.js',
            'web/fx/scripts/rma.js',
            'web/fx/scripts/jquery.placeholder-enhanced.min.js',
            'web/fx/scripts/oldie.js'
        ]
    };

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        watch: {
            scss: {
                tasks: ['compass', 'cssmin', 'modernizr'],
                files: files.scss
            },
            js: {
                tasks: ['modernizr', 'uglify'],
                files: files.desktop_js.concat(files.mobile_js)
            }
        },

        compass : {
            desktop: {
                options: {
                    outputStyle:    'compact',
                    basePath:       'web/fx/2013s1',
                    sassDir:        'sass',
                    cssDir:         'css',
                    imagesDir:      '../images',
                    javascriptsDir: 'scripts',
                    noLineComments: true,
                    httpPath:       '/fx/2013s1/'
                }
            },
            mobile: {
                options: {
                    outputStyle:    'compact',
                    basePath:       'web/fx/2013s1_mobile',
                    sassDir:        'sass',
                    cssDir:         'css',
                    imagesDir:      '../images',
                    javascriptsDir: 'scripts',
                    noLineComments: true,
                    httpPath:       '/fx/mobile/',
                    importPath:     'web/fx/2013s1'
                }
            }
        },

        cssmin: {
            desktop: {
                options: {
                    keepSpecialComments: 0
                },
                src: files.mobile_css,
                dest: 'web/fx/build/mobile.min.css'
            },
            mobile: {
                options: {
                    keepSpecialComments: 0
                },
                src: files.desktop_css,
                dest: 'web/fx/build/desktop.min.css'
            }
        },

        modernizr: {
            desktop: {
                devFile: 'remote',
                parseFiles: true,
                files: {
                    src: files.scss.concat(
                        files.desktop_css,
                        files.mobile_css,
                        files.desktop_js,
                        files.mobile_js,
                        files.consultant_js
                    )
                },
                outputFile: 'web/fx/build/modernizr-custom.js',
                "extra" : {
                    "shiv" : true,
                    "printshiv" : false,
                    "load" : true,
                    "mq" : false,
                    "cssclasses" : true
                },
                "extensibility" : {
                    "addtest" : false,
                    "prefixed" : false,
                    "teststyles" : false,
                    "testprops" : false,
                    "testallprops" : false,
                    "hasevents" : false,
                    "prefixes" : false,
                    "domprefixes" : false
                }
            }
        },

        uglify: {
            desktop: {
                options: {
                    mangle: false,
                    sourceMap: true
                },
                files: {
                    'web/fx/build/desktop.min.js' : files.desktop_js
                }
            },
            mobile: {
                options: {
                    mangle: false,
                    sourceMap: true
                },
                files: {
                    'web/fx/build/mobile.min.js' : files.mobile_js
                }
            },
            admin: {
                options: {
                    mangle: false,
                    sourceMap: true
                },
                files: {
                    'web/fx/build/admin.min.js' : files.admin_js
                }
            },
            consultant: {
                options: {
                    mangle: false,
                    sourceMap: true
                },
                files: {
                    'web/fx/build/consultant.min.js' : files.consultant_js
                }
            }
        }

    });

    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks("grunt-modernizr");
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('default', ['watch']);
    grunt.registerTask('build', ['compass', 'cssmin', 'modernizr', 'uglify']);
};

