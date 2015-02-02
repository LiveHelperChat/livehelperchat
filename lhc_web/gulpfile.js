var gulp = require('gulp'),
gutil    = require('gulp-util'),
uglify   = require('gulp-uglify'),
concat   = require('gulp-concat');
watch 	 = require('gulp-watch');

gulp.task('js-cobrowse-operator', function() {
	var stylePath = ['design/defaulttheme/js/cobrowse/mutation-summary.js',
	                 'design/defaulttheme/js/cobrowse/tree-mirror.js',
	                 'design/defaulttheme/js/cobrowse/jquery.selector.js',
	                 'design/defaulttheme/js/cobrowse/lhc_operator.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('cobrowse.operator.min.js'))
	.pipe(uglify({preserveComments: 'some'}))
	.pipe(gulp.dest('design/defaulttheme/js/cobrowse/compiled'));
});

gulp.task('js-cobrowse-visitor', function() {
    var stylePath = ['design/defaulttheme/js/cobrowse/mutation-summary.js',
                     'design/defaulttheme/js/cobrowse/tree-mirror.js',
                     'design/defaulttheme/js/cobrowse/lhc.js'];
        
    return gulp.src(stylePath)
        .pipe(concat('cobrowse.visitor.min.js'))
        .pipe(uglify({preserveComments: 'some'}))
        .pipe(gulp.dest('design/defaulttheme/js/cobrowse/compiled'));
});

gulp.task('default', ['js-cobrowse-operator','js-cobrowse-visitor'], function() {
	// Just execute all the tasks	
});

gulp.task('watch', function () {
	gulp.watch('design/defaulttheme/js/cobrowse/*.js', ['js-cobrowse-visitor','js-cobrowse-operator']);
});