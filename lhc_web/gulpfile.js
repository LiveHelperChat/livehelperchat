var gulp = require('gulp'),
gutil    = require('gulp-util'),
uglify   = require('gulp-uglify'),
concat   = require('gulp-concat'),
watch 	 = require('gulp-watch'),
webpack = require('webpack'),
webpackConfig = require('./webpack.config.js');
bower = require('gulp-bower');
//rm = require('gulp-rimraf');

gulp.task('js-cobrowse-operator', function() {
	var stylePath = ['design/defaulttheme/js/cobrowse/mutation-summary.js',
	                 'design/defaulttheme/js/cobrowse/tree-mirror.js',
	                 'design/defaulttheme/js/cobrowse/jquery.selector.js'];
	
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

gulp.task('js-angular-main', function() {
	var stylePath = ['design/defaulttheme/js/angular.lhc.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('angular.lhc.min.js'))
	.pipe(uglify({preserveComments: 'some'}))
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-angular-online', function() {
	var stylePath = ['design/defaulttheme/js/angular.lhc.online.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('angular.lhc.online.min.js'))
	.pipe(uglify({preserveComments: 'some'}))
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-main-fileupload', function() {
	var stylePath = [
	                 'design/defaulttheme/js/fileupload/jquery.ui.widget.js',
	                 'design/defaulttheme/js/fileupload/jquery.iframe-transport.js',
	                 'design/defaulttheme/js/fileupload/jquery.fileupload.js'
	                 ];
	
	return gulp.src(stylePath)
	.pipe(concat('jquery.fileupload.min.js'))
	.pipe(uglify({preserveComments: 'some'}))
	.pipe(gulp.dest('design/defaulttheme/js/fileupload'));
});

gulp.task('js-datepicker', function() {
	var stylePath = ['design/defaulttheme/js/datepicker.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('datepicker.min.js'))
	.pipe(uglify({preserveComments: 'some'}))
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lhc-speak-js', function() {
	var stylePath = ['design/defaulttheme/js/lhc.speak.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('lhc.speak.min.js'))
	.pipe(uglify({preserveComments: 'some'}))
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lh', function() {
	var stylePath = ['design/defaulttheme/js/lh.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('lh.min.js'))
	.pipe(uglify({preserveComments: 'some'}))
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lh-npm', function() {		 
	 webpack(webpackConfig, function(err, stats) {
	        if(err) throw new gutil.PluginError("webpack", err);
	        gutil.log("[webpack]", stats.toString({
	            // output options
	        }));	            	
	 });
});

gulp.task('bower', function() {
	return bower()
	.pipe(gulp.dest("./bower_components"));
});

gulp.task('bower-move-bootstrap',['bower'], function() {
	gulp.src('./bower_components/bootstrap/dist/*/**.*').pipe(gulp.dest('./design/defaulttheme/vendor/bootstrap'));
});

gulp.task('bower-move-bootstrap-font',['bower'], function() {
	gulp.src('./bower_components/bootstrap/dist/fonts/**.*').pipe(gulp.dest('./design/defaulttheme/fonts'));
});

gulp.task('bower-move-jquery',['bower'], function() {
	gulp.src('./bower_components/jquery/dist/**.*').pipe(gulp.dest('./design/defaulttheme/vendor/jquery'));  
});

gulp.task('bower-setup',['bower-move-bootstrap','bower-move-jquery','bower-move-bootstrap-font'], function() {
	
});

gulp.task('js-cobrowse',['js-cobrowse-operator','js-cobrowse-visitor'], function() {

});


gulp.task('default', ['js-cobrowse-operator','js-cobrowse-visitor','js-angular-main','js-main-fileupload','js-datepicker','js-lhc-speak-js','js-lh','js-angular-online','js-lh-npm','bower-setup'], function() {
	// Just execute all the tasks	
});

gulp.task('webpack', ['js-lh-npm'], function() {
	// Just execute all the tasks	
});

gulp.task('watch', function () {
	gulp.watch('design/defaulttheme/js/cobrowse/*.js', ['js-cobrowse-visitor','js-cobrowse-operator']);	
	gulp.watch('design/defaulttheme/js/lh.js', ['js-lh']);
	gulp.watch(['design/defaulttheme/js/lh/lh.js','design/defaulttheme/js/lh/lh-modules/*.js'], ['js-lh-npm']);
});