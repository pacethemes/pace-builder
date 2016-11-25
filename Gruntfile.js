'use strict';
module.exports = function (grunt) {

    var SOURCE_DIR = './',
        VVV_DIR = '../../../../../',
        FILES = [
                    '**',
                    '!node_modules/**',
                    '!**/.{svn,git}/**', // Ignore version control directories.
                    // Ignore unminified versions of external libs we don't ship:
                    '!**tests/**', 
                    '!**sass/**', 
                    '!*.{scss,sass}', 
                    '!.DS_Store', 
                    '!.sass-cache', 
                    '!karma.conf.js', 
                    '!Gruntfile.js', 
                    '!phpunit.xml', 
                    '!package.json'
                ];

    // load all grunt tasks
    require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),

        notify_hooks: {
            options: {
                enabled: true,
                max_jshint_notifications: 5, // maximum number of notifications from jshint output
                title: "Project Name", // defaults to the name in package.json, or will use project directory's name
                success: false, // whether successful grunt executions should be notified automatically
                duration: 3 // the duration of notification in seconds, for `notify-send only
            }
        },

        notify: {
            // task_name: {
            //     options: {
            //         // Task-specific options go here.
            //     }
            // },
            watch: {
                options: {
                    title: 'Task Complete', // optional
                    message: 'SASS and Uglify finished running', //required
                }
            },
            server: {
                options: {
                    message: 'Server is ready!'
                }
            }
        },

        // watch for changes and trigger sass, jshint, uglify and livereload
        watch: {
            // options: {
            //     livereload: true,
            // },
            sass: {
                files: ['**/*.scss'],
                tasks: ['sass', /*'postcss', 'concat:css', 'cssmin'*/ 'notify:watch']
            },
            js: {
                files: '<%= jshint.all %>',
                tasks: ['jshint', 'uglify', 'concat:js']
            }
            // livereload: {
            //     files: ['*.html', '*.php', 'assets/images/**/*.{png,jpg,jpeg,gif,webp,svg}']
            // }
        },

        // sass
        sass: {
            dist: {
                options: { // Target options
                    style: 'expanded'
                },
                files: {
                    'assets/css/pacebuilder-admin.css': 'assets/sass/pacebuilder-admin.scss',
                    'assets/css/settings.css': 'assets/sass/settings.scss',
                    'assets/css/pacebuilder.css': 'assets/sass/pacebuilder.scss'
                }
            }
        },

        csscss: {
            dist: {
                src: ['assets/sass/pacebuilder-admin.scss']
            }
        },

        copy: {
            // main: {
            //     expand: true,
            //     src: 'src/*',
            //     dest: 'dest/',
            //   },
            main: {
                files: [
                    {
                        dot: true,
                        expand: true,
                        cwd: SOURCE_DIR,
                        src: FILES,
                        dest: VVV_DIR + '/www/pb/htdocs/wp-content/plugins/pace-builder/'
                    },
                    {
                        dot: true,
                        expand: true,
                        cwd: SOURCE_DIR,
                        src: FILES,
                        dest: VVV_DIR + '/www/quest/htdocs/wp-content/plugins/pace-builder/'
                    },
                    {
                        dot: true,
                        expand: true,
                        cwd: SOURCE_DIR,
                        src: FILES,
                        dest: VVV_DIR + '/../wp-vagrant/wordpress/build/wp-content/plugins/pace-builder/'
                    }
                ]
            }
        },

        postcss: {
            options: {
                map: true, // inline sourcemaps

                // or
                // map: {
                //     inline: false, // save all sourcemaps as separate files...
                //     // annotation: 'dist/css/maps/' // ...to the specified directory
                // },

                processors: [
                    require('autoprefixer')({
                        browsers: [
                            'Android >= 2.1',
                            'Chrome >= 21',
                            'Edge >= 12',
                            'Explorer >= 7',
                            'Firefox >= 17',
                            'Opera >= 12.1',
                            'Safari >= 6.0'
                        ],
                        cascade: false
                    }) // add vendor prefixes
                    // require('cssnano')() // minify the result
                ]
            },
            dist: {
                src: 'assets/css/*.css'
            }
        },

        // javascript linting with jshint
        jshint: {
            options: {
                jshintrc: '.jshintrc',
                "force": true
            },
            all: [
                'assets/js/builder/app.js',
                'assets/js/builder/collections.js',
                'assets/js/builder/models.js',
                'assets/js/builder/util.js',
                'assets/js/builder/views.js',
                'assets/js/pace-builder.js'
            ]
        },

        phplint: {
            options: {
                phpArgs: {
                    // '-lf': null
                }
            },
            all: {
                src: [
                    '*.php',
                    '**/*.php',
                    '!node_modules/**'
                ]
            }
        },

        'phpmd-runner': {
            options: {
                phpmd: '/usr/local/bin/phpmd',
                reportFormat: 'html',
                reportFile: 'md.html',
                rulesets: [
                    'cleancode',
                    'codesize',
                    // 'controversial',
                    'design',
                    'naming',
                    'unusedcode'
                ],
                strict: true
            },
            files: ['**/*.php', '!node_modules/**', '!includes/CMB2/**']
        },

        concat: {
            js: {
                options: {
                    banner: '/*! <%= pkg.title %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %>\n' +
                    ' * Copyright (c) <%= grunt.template.today("yyyy") %>\n' +
                    ' * Licensed GPLv2+ \n' +
                    ' */\n',
                    process: function (src, filepath) {
                        return '\n// Source: ' + filepath + '\n' + src;
                    }
                },
                src: [
                    
                ],
                dest: ''
            },
            css: {
                options: {
                    banner: '/*! <%= pkg.title %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %>\n' +
                    ' * Copyright (c) <%= grunt.template.today("yyyy") %>\n' +
                    ' * Licensed GPLv2+ \n' +
                    ' */\n',
                    process: function (src, filepath) {
                        return '\n/* Source: ' + filepath + '*/\n' + src;
                    }
                },
                src: [
                ],
                dest: ''
            }
        },

        cssmin: {
            dist: {
                options: {
                    banner: '/*! <%= pkg.title %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %>\n' +
                    ' * <%= pkg.homepage %>\n' +
                    ' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
                    ' * Licensed GPLv2+' +
                    ' */\n'
                },
                files: {
                    'assets/css/pacebuilder.min.css': ['assets/css/pacebuilder.css'],
                    'assets/css/admin.min.css': ['assets/css/pacebuilder-admin.css', 'assets/css/settings.css']
                }
            }
        },

        // uglify to concat, minify, and make source maps
        uglify: {
            dist: {
                options: {
                    mangle: false,
                    banner: '/*! <%= pkg.title %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %>\n' +
                    ' * Copyright (c) <%= grunt.template.today("yyyy") %>; \n' +
                    ' * Licensed GPLv2+' +
                    ' */\n'
                },
                files: {
                    'assets/plugins/mapsed/mapsed.min.js': ['assets/plugins/mapsed/mapsed.js'],
                    'assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js': ['assets/plugins/ion-rangeslider/js/ion.rangeSlider.js'],
                    'assets/js/admin-plugins.min.js': [
                                                        'assets/plugins/timepicker/jquery-ui-timepicker-addon.min.js',
                                                        'assets/plugins/backbone-marionette/backbone.marionette.min.js',
                                                        'assets/plugins/backbone-modal/backbone.modal.js',
                                                        'assets/plugins/backbone-modal/backbone.marionette.modals.js',
                                                        'assets/plugins/jquery.onoff/jquery.onoff.min.js',
                                                        'assets/plugins/serialize-object/jquery.serialize-object.min.js',
                                                        'assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js',
                                                        'assets/plugins/wp-color-picker-alpha/wp-color-picker-alpha.min.js',
                                                        'assets/plugins/chosen/chosen.jquery.min.js',
                                                        'assets/plugins/mapsed/mapsed.min.js'
                                                ],
                    'assets/js/admin-builder.min.js': [ 
                                                        'assets/js/builder/util.js',
                                                        'assets/js/builder/models.js',
                                                        'assets/js/builder/collections.js',
                                                        'assets/js/builder/views.js',
                                                        'assets/js/builder/app.js'
                                                ],
                    // frontend
                    // 'assets/js/public.min.js': ['assets/js/public.js'],
                    'assets/js/public-all.min.js': [ 
                                                        'assets/plugins/bootstrap/js/bootstrap.min.js',
                                                        'assets/plugins/wow/wow.min.js',
                                                        'assets/plugins/parallax/jquery.parallax.js',
                                                        'assets/plugins/slicknav/jquery.slicknav.min.js',
                                                        'assets/plugins/colorbox/jquery.colorbox-min.js',
                                                        'assets/plugins/flexslider/jquery.flexslider-min.js',
                                                        'assets/plugins/color/jquery.color.js',
                                                        'assets/js/public.js'
                                                ]
                }
            }
        },

        addtextdomain: {
            options: {
                textdomain: 'pace-builder', // Project text domain.
                updateDomains: [] // List of text domains to replace.
            },
            target: {
                files: {
                    src: [
                        '*.php',
                        '**/*.php',
                        '!node_modules/**',
                        '!tests/**'
                    ]
                }
            }
        },

        makepot: {
            target: {
                options: {
                    domainPath: 'languages',
                    mainFile: 'pace-builder.php',
                    include: [
                        '[^*?"<>]*.php',
                        '!node_modules/**',
                    ],
                    type: 'wp-plugin',
                    processPot: function (pot) {
                        var translation,
                            excluded_meta = [
                                'Theme URI of the plugin/theme',
                                'Theme Name of the plugin/theme',
                                'Author of the plugin/theme',
                                'Author URI of the plugin/theme'
                            ];

                        for (translation in pot.translations['']) {
                            if ('undefined' !== typeof pot.translations[''][translation].comments.extracted) {
                                if (excluded_meta.indexOf(pot.translations[''][translation].comments.extracted) >= 0) {
                                    console.log('Excluded meta: ' + pot.translations[''][translation].comments.extracted);
                                    delete pot.translations[''][translation];
                                }
                            }
                        }

                        return pot;
                    }
                }
            }
        },

        compress: {
            dist: {
                options: {
                    archive: '../dist/pace-builder.<%= pkg.version %>.zip',
                    mode: 'zip'
                },
                files: [{
                    dest: 'pace-builder',
                    src: ['**/*', '!**node_modules/**', '!**tests/**', '!**sass/**', '!*.{scss,sass}', '!.DS_Store', '!.sass-cache', '!karma.conf.js', '!Gruntfile.js', '!phpunit.xml', '!package.json', '!*config.codekit']
                }]
            }
        }

    });

    grunt.registerTask('updateVersion', 'Update Plugin version to the latest version from package.json file', function () {
        var pluginFile = grunt.file.read('pace-builder.php'),
            regex = /\* Version\:(\s+)(\d*\.?\d*\.?\d*)/,
            pkg = grunt.file.readJSON('package.json'),
            versionStr = pluginFile.match(regex);
        if (versionStr.length && versionStr.length > 2 && parseFloat(versionStr[2]) !== pkg.version) {
            pluginFile = pluginFile.replace(regex, '* Version:' + versionStr[1] + pkg.version);
            grunt.log.writeln('Plugin version is ' + versionStr[2] + ' in the pace-builder.php file and ' + pkg.version + ' in the package.json file');
            grunt.log.writeln('Updating the Plugin version in the pace-builder.php file');
            grunt.file.write('pace-builder.php', pluginFile);
        }
    });


    // register task
    grunt.registerTask('default', ['watch', 'notify:watch']);
    grunt.registerTask('assets', ['sass', 'uglify', 'postcss', 'cssmin']);
    grunt.registerTask('package', ['assets', 'compress']);

};
