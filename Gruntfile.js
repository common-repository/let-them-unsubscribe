module.exports = function(grunt) {
    require('load-grunt-tasks')(grunt);
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        checktextdomain: {
            options:{
                report_missing: false,
                text_domain: 'let-them-unsubscribe',
                keywords: [
                    '__:1,2d',
                    '_e:1,2d',
                    '_x:1,2c,3d',
                    'esc_html__:1,2d',
                    'esc_html_e:1,2d',
                    'esc_html_x:1,2c,3d',
                    'esc_attr__:1,2d',
                    'esc_attr_e:1,2d',
                    'esc_attr_x:1,2c,3d',
                    '_ex:1,2c,3d',
                    '_n:1,2,4d',
                    '_nx:1,2,4c,5d',
                    '_n_noop:1,2,3d',
                    '_nx_noop:1,2,3c,4d'
                ]
            },
            files: {
                src:  [
                    '**/*.php', // Include all files
                    '!node_modules/**', // Exclude node_modules/
                    '!tests/**', // Exclude tests/
                    '!includes/external/**',
                    '!build/**'
                ],
                expand: true
            }
        },

        copy: {
            main: {
                src:  [
                    '**',
                    '!npm-debug.log',
                    '!node_modules/**',
                    '!build/**',
                    '!bin/**',
                    '!.git/**',
                    '!Gruntfile.js',
                    '!package.json',
                    '!.gitignore',
                    '!.gitmodules',
                    '!sourceMap.map',
                    '!phpunit.xml.dist',
                    '!travis.yml',
                    '!tests/**',
                    '!**/Gruntfile.js',
                    '!**/package.json',
                    '!**/README.md',
                    '!lite-vs-pro.txt',
                    '!composer.json',
                    '!vendor/**',
                    '!tmp/**',
                    '!**/*~',
                    '!lang/let-them-unsubscribe.mo',
                    '!lang/let-them-unsubscribe.po'
                ],
                dest: 'build/<%= pkg.name %>/'
            }
        },

        // Generate POT files.
        makepot: {
            options: {
                type: 'wp-plugin',
                domainPath: 'lang',
                potHeaders: {
                    "Project-Id-Version": "Let Them Unsubscribe",
                    "Last-Translator": "Ignacio",
                    "Language-Team": "Ignacio Cruz",
                }
            },
            dist: {
                options: {
                    potFilename: 'let-them-unsubscribe.pot',
                    exclude: [
                        'tests/.*',
                        'node_modules/.*',
                        'includes/external/*',
                        'build/*'
                    ]
                }
            }
        },

        clean: {
            main: ['build/*']
        },


        search: {
            files: {
                src: ['<%= pkg.main %>']
            },
            options: {
                logFile: 'tmp/log-search.log',
                searchString: /^[ \t\/*#@]*Version:(.*)$/mig,
                onMatch: function(match) {
                    var regExp = /^[ \t\/*#@]*Version:(.*)$/mig;
                    var groupedMatches = regExp.exec( match.match );
                    var versionFound = groupedMatches[1].trim();
                    if ( versionFound != grunt.file.readJSON('package.json').version ) {
                        grunt.fail.fatal("Plugin version does not match with package.json version. Please, fix.");
                    }
                },
                onComplete: function( matches ) {
                    if ( ! matches.numMatches ) {
                        if ( ! grunt.file.readJSON('package.json').main ) {
                            grunt.fail.fatal("main field is not defined in package.json. Please, add the plugin main file on that field.");
                        }
                        else {
                            grunt.fail.fatal("Version Plugin header not found in " + grunt.file.readJSON('package.json').main + " file or the file does not exist" );
                        }

                    }
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-search');

    grunt.registerTask('version-compare', [ 'search' ] );

    grunt.registerTask('build', [
        'version-compare',
        'clean',
        'checktextdomain',
        'makepot',
        'copy'
    ]);

    grunt.registerTask('build:beta', [
        'version-compare',
        'clean',
        'checktextdomain',
        'makepot',
        'copy'
    ]);

    grunt.registerTask('po', [
        'checktextdomain',
        'makepot'
    ]);
};