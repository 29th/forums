'use strict';

var gulp = require('gulp')
    // Load all Gulp tasks matching the `gulp-*` pattern
  , $ = require('gulp-load-plugins')();

gulp.task('styles', function () {
  var dependencies = require('wiredep')();

  return gulp.src(dependencies.css)
    .pipe($.plumber())
    .pipe($.concat('editor.css'))
    .pipe(gulp.dest('design'))
    .pipe($.size({showFiles: true}));
});

gulp.task('scripts', function () {
  var dependencies = require('wiredep')();

  return gulp.src((dependencies.js || []).concat([
    'js/src/main.js'
  ]))
    .pipe($.plumber())
    .pipe($.concat('editor.js'))
    .pipe(gulp.dest('js'))
    .pipe($.size({showFiles: true}));
});

gulp.task('default', ['styles', 'scripts']);

gulp.task('watch', function () {
  var server = $.livereload();

  gulp.watch([
    'js/*.js'
  ], function (file) {
    return server.changed(file.path);
  });

  gulp.watch('js/src/**/*.js', ['scripts']);
});

// Expose Gulp to external tools
module.exports = gulp;
