module.exports = function(grunt) {
	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		autoprefixer: {
			options: {
			},
			no_dest: {
				src : 'public/css/cms.css'
			}
		},
		copy: {
			main: {
				files: [
					{
						expand: true,
						flatten: true,
						src: ['bower_components/fontawesome/fonts/*'],
						dest: 'public/fonts/',
						filter: 'isFile'
					},
					{
						expand: true,
						flatten: true,
						src: ['bower_components/leaflet/dist/images/*'],
						dest: 'public/images/',
						filter: 'isFile'
					}
				]
			}
		},
		concat: {
			options: {
				separator: ';',
				process: function(src, filepath) {
				  return src.replace(/@VERSION/g, grunt.config.get('pkg.version'));
				}
			},
			dist: {
	  			src: [
					'bower_components/modernizr/modernizr.js',
					'bower_components/jquery/dist/jquery.js',
					'bower_components/jquery-ui/jquery-ui.js',
					'src/js/string.js',
					'bower_components/jgrowl/jquery.jgrowl.js',
					'bower_components/tablesorter/jquery.tablesorter.js',
					'bower_components/datetimepicker/jquery.datetimepicker.js',
					'bower_components/blueimp-canvas-to-blob/js/canvas-to-blob.js',
					'bower_components/jquery-file-upload/js/jquery.fileupload.js',
					'bower_components/leaflet/dist/leaflet.js',
					'bower_components/caman/dist/caman.full.js',
					'bower_components/jcrop/js/Jcrop.js',
					'src/js/boomcms/plugins.js',
					'src/js/boomcms/config.js',
					'src/js/boomcms/loader.js',
					'src/js/boomcms/notification.js',
					'src/js/boomcms/core.js',
					'src/js/boomcms/history.js',
					'src/js/boomcms/log.js',
					'src/js/boomcms/dialog.js',
					'src/js/boomcms/alert.js',
					'src/js/boomcms/confirmation.js',
					'bower_components/pushy/js/pushy.js',
					'src/js/boomcms/tagAutocomplete.js',
					'src/js/boomcms/page/status.js',
					'src/js/boomcms/page/page.js',
					'src/js/boomcms/page/settings.js',
					'src/js/boomcms/page/editor.js',
					'src/js/boomcms/page/toolbar.js',
					'src/js/boomcms/page/tree.js',
					'src/js/boomcms/page/tagSearch.js',
					'src/js/boomcms/page/tagAutocomplete.js',
					'src/js/boomcms/page/url.js',
					'src/js/boomcms/page/settings/*.js',
					'src/js/boomcms/textEditor.js',
					'src/js/boomcms/chunk.js',
					'src/js/boomcms/chunk/chunk.js',
					'src/js/boomcms/chunk/text.js',
					'src/js/boomcms/chunk/linkset.js',
					'src/js/boomcms/chunk/feature.js',
					'src/js/boomcms/chunk/asset.js',
					'src/js/boomcms/chunk/slideshow.js',
					'src/js/boomcms/chunk/timestamp.js',
					'src/js/boomcms/chunk/library.js',
					'src/js/boomcms/chunk/slideshow/editor.js',
					'src/js/boomcms/chunk/linkset/editor.js',
					'src/js/boomcms/chunk/asset/editor.js',
					'src/js/boomcms/chunk/pageTags.js',
					'src/js/boomcms/chunk/pageVisibility.js',
					'src/js/boomcms/chunk/link.js',
					'src/js/boomcms/chunk/location.js',
					'src/js/boomcms/chunk/location/editor.js',
					'src/js/boomcms/chunk/html.js',
					'src/js/boomcms/page/title.js',
					'src/js/boomcms/link/link.js',
					'src/js/boomcms/link/picker.js',
					'src/js/boomcms/template/manager.js',
					'src/js/boomcms/asset/asset.js',
					'src/js/boomcms/asset/editor.js',
					'src/js/boomcms/asset/manager.js',
					'src/js/boomcms/asset/picker.js',
					'src/js/boomcms/asset/titleFilter.js',
					'src/js/boomcms/asset/uploader.js',
					'src/js/boomcms/asset/justify.js',
					'src/js/boomcms/asset/tagAutocomplete.js',
					'src/js/boomcms/asset/tagSearch.js',
					'src/js/boomcms/asset/selection.js',
					'src/js/boomcms/group/group.js',
					'src/js/boomcms/group/permissionsEditor.js',
					'src/js/boomcms/person.js',
					'src/js/boomcms/peopleManager.js',
					'src/js/boomcms/imageEditor.js',
					'src/js/boomcms/approvals.js',
					'bower_components/wysihtml/dist/wysihtml-toolbar.js',
					'src/js/wysihtml5/parser_rules/full.js',
					'src/js/wysihtml5/parser_rules/inline.js',
					'src/js/wysihtml5/commands/insertBoomAsset.js',
					'src/js/wysihtml5/commands/createBoomLink.js',
					'src/js/wysihtml5/commands/cta.js',
					'src/js/wysihtml5/commands/insertSuperscript.js',
					'src/js/wysihtml5/commands/insertSubscript.js',
					'src/js/jquery/jqpagination.js',
					'src/js/boomcms/page/manager.js'
				],
				dest: 'public/js/cms.js'
			}
		},
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> (<%= grunt.template.today("yyyy-mm-dd") %>) */\n',
				sourceMap: true
			},
			build: {
				files: {
					'public/js/cms.min.js': 'public/js/cms.js'
				}
			}
		},
		less: {
			production: {
				options: {
					paths: ["src/css"]
				},
				files: {
					"public/css/cms.css": "src/css/cms.less",
					"public/css/inpage.css": "src/css/inpage.less",
					"public/css/default-template.css": "src/css/default-template.less"
				}
			}
		},
		cssmin: {
			options: {
				shorthandCompacting: false,
				roundingPrecision: -1
			},
			target: {
				files: {
					'public/css/default-template.css': [
						'bower_components/normalize.css/normalize.css',
						'public/css/default-template.css'
					],
					'public/css/inpage.css': [
						'public/css/inpage.css'
					],
					'public/css/cms.css': [
						'bower_components/normalize.css/normalize.css',
						'bower_components/datetimepicker/jquery.datetimepicker.css',
						'bower_components/jquery-ui/themes/base/autocomplete.css',
						'bower_components/leaflet/dist/leaflet.css',
						'bower_components/jcrop/css/Jcrop.css',
						'src/css/libraries/jqpagination.css',
						'public/css/cms.css'
					 ]
				}
			}
		},
		watch: {
			css: {
				files: 'src/css/**/*.less',
				tasks: ['build-css']
			},
			js: {
				files: 'src/js/**/*.js',
				tasks: ['build-js']
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-autoprefixer');
	grunt.loadNpmTasks('grunt-contrib-copy');

	grunt.registerTask('build-css', ['less', 'autoprefixer:no_dest', 'cssmin']);
	grunt.registerTask('build-js', ['concat:dist', 'uglify']);
	grunt.registerTask('dist', ['copy', 'build-css', 'build-js']);
	grunt.registerTask('default',['watch']);
};
