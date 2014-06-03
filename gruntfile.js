module.exports = function (grunt) {
    "use strict";

    var desktop_files = grunt.file.readJSON('app/Resources/themes/2013s1/resources.json');
    var mobile_files  = grunt.file.readJSON('app/Resources/themes/2013s1_mobile/resources.json');

    var files = {
        scss : [
            'web/fx/2013s1/sass/*.scss',
            'web/fx/2013s1_mobile/sass/*.scss'
        ],

        desktop_css   : desktop_files.desktop_css,
        desktop_js    : desktop_files.desktop_js,
        admin_js      : desktop_files.admin_js,
        consultant_js : desktop_files.consultant_js,

        mobile_css    : mobile_files.mobile_css,
        mobile_js     : mobile_files.mobile_js
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
                files: files.desktop_js.concat(files.mobile_js, files.admin_js, files.consultant_js)
            }
        },

        compass : {
            desktop: {
                options: {
                    // outputStyle:    'compressed',
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
                    // outputStyle:    'compressed',
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
                    "shiv" : false,
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

