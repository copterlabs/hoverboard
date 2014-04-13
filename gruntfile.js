module.exports = function(grunt) {
 
    grunt.registerTask('watch', [ 'watch' ]);
   
    grunt.initConfig({
        uglify: {
            options: {
                mangle: true,
                compress: true,
                preserveComments: 'some'
            },
            js: {
                files: {
                    'assets/js/main.min.js': ['assets/js/main.js', 'assets/js/init.js']
                }
            }
        },
        less: {
            style: {
                options: {
                    cleancss: true,
                },
                files: {
                    'assets/css/main.min.css': 'assets/less/main.less',
                }
            }
        },
        watch: {
            js: {
                files: ['assets/js/*.js'],
                tasks: ['uglify:js'],
                options: {
                    livereload: true,
                }
            },
            css: {
                files: [
                    'assets/less/*.less',
                ],
                tasks: ['less:style'],
                options: {
                    livereload: true,
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-notify');
};
