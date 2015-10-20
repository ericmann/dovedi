module.exports = function( grunt ) {

	// Project configuration
	grunt.initConfig( {
		pkg:    grunt.file.readJSON( 'package.json' ),
		clean: {
			main: ['release/<%= pkg.version %>']
		},
		copy: {
			// Copy the plugin to a versioned release directory
			main: {
				src:  [
					'**',
					'!**/.*',
					'!**/readme.md',
					'!node_modules/**',
					'!vendor/**',
					'!tests/**',
					'!release/**',
					'!assets/css/sass/**',
					'!assets/css/src/**',
					'!assets/js/src/**',
					'!images/src/**',
					'!bootstrap.php',
					'!bower.json',
					'!composer.json',
					'!composer.lock',
					'!Gruntfile.js',
					'!package.json',
					'!phpunit.xml',
					'!phpunit.xml.dist'
				],
				dest: 'release/<%= pkg.version %>/'
			}
		},
		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './release/dovedi.<%= pkg.version %>.zip'
				},
				expand: true,
				cwd: 'release/<%= pkg.version %>/',
				src: ['**/*'],
				dest: 'dovedi/'
			}
		},
		wp_readme_to_markdown: {
			readme: {
				files: {
					'readme.md': 'readme.txt'
				}
			}
		},
		phpunit: {
			classes: {
				dir: 'tests/phpunit/'
			},
			options: {
				bin: 'vendor/bin/phpunit',
				bootstrap: 'bootstrap.php.dist',
				colors: true,
				testSuffix: 'Tests.php'
			}
		},
		makepot: {
			target: {
				options: {
					cwd: '',
					domainPath: 'languages',
					mainFile: 'dovedi.php',
					potFilename: 'dovedi.pot',
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true
					},
					processPot: null,
					type: 'wp-plugin',
					updateTimestamp: true,
					updatePoFiles: false
				}
			}
		}
	} );

	// Load tasks
	require('load-grunt-tasks')(grunt);

	// Register tasks

	grunt.registerTask( 'build', ['clean', 'copy', 'compress'] );

	grunt.registerTask( 'test', ['phpunit'] );

	grunt.registerTask( 'default', ['test'] );

	grunt.util.linefeed = '\n';
};
