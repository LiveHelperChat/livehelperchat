var gulp = require('gulp'),
	PluginError = require('plugin-error'),
	terser = require('gulp-terser'),
	concat = require('gulp-concat'),
	webpack = require('webpack'),
	webpackConfig = require('./webpack.config.js');
var babel = require('gulp-babel');
var newer = require('gulp-newer');
var eslint = require('gulp-eslint-new');
var sourcemaps = require('gulp-sourcemaps');
var browserify = require('browserify');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');

gulp.task('js-hotkeys', function () {
	var stylePath = ['design/defaulttheme/js/jquery.hotkeys.js'];

	return gulp.src(stylePath)
		.pipe(concat('jquery.hotkeys.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-cobrowse-operator', function () {
	var stylePath = ['design/defaulttheme/js/cobrowse/mutation-summary.js',
		'design/defaulttheme/js/cobrowse/tree-mirror.js',
		'design/defaulttheme/js/cobrowse/jquery.selector.js',
		'design/defaulttheme/js/cobrowse/lhc_operator.js'];

	return gulp.src(stylePath)
		.pipe(concat('cobrowse.operator.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js/cobrowse/compiled'));
});

gulp.task('js-cobrowse-visitor', function () {
	var stylePath = ['design/defaulttheme/js/cobrowse/mutation-summary.js',
		'design/defaulttheme/js/cobrowse/tree-mirror.js',
		'design/defaulttheme/js/cobrowse/lhc.js'];

	return gulp.src(stylePath)
		.pipe(concat('cobrowse.visitor.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js/cobrowse/compiled'));
});

gulp.task('js-angular-main', function () {
	var stylePath = ['design/defaulttheme/js/angular.lhc.js'];

	return gulp.src(stylePath)
		.pipe(concat('angular.lhc.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-modal-ext', function () {
	var stylePath = ['design/defaulttheme/js/modal.ext.js'];
	return gulp.src(stylePath)
		.pipe(concat('modal.ext.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js'));
});


gulp.task('js-angular-online', function () {
	var stylePath = ['design/defaulttheme/js/angular.lhc.online.js'];

	return gulp.src(stylePath)
		.pipe(concat('angular.lhc.online.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-angular-checkmodel', function () {
	var stylePath = ['design/defaulttheme/js/checklist-model.js'];

	return gulp.src(stylePath)
		.pipe(concat('checklist-model.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-main-fileupload', function () {
	var stylePath = [
		'design/defaulttheme/js/fileupload/jquery.ui.widget.js',
		'design/defaulttheme/js/fileupload/jquery.iframe-transport.js',
		'design/defaulttheme/js/fileupload/jquery.fileupload.js'
	];

	return gulp.src(stylePath)
		.pipe(concat('jquery.fileupload.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js/fileupload'));
});

gulp.task('js-datepicker', function () {
	var stylePath = ['design/defaulttheme/js/datepicker.js'];

	return gulp.src(stylePath)
		.pipe(concat('datepicker.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lhc-speak-js', function () {
	var stylePath = ['design/defaulttheme/js/lhc.speak.js'];

	return gulp.src(stylePath)
		.pipe(concat('lhc.speak.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lh', function () {
	var stylePath = ['design/defaulttheme/js/lh.js'];

	return gulp.src(stylePath)
		.pipe(concat('lh.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lh-legacy', function () {
	var stylePath = ['design/defaulttheme/js/lh.legacy.js'];
	return gulp.src(stylePath)
		.pipe(concat('lh.legacy.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lh-plugin', function () {
	var stylePath = ['design/defaulttheme/js/lhc.dropdown.plugin.js'];
	return gulp.src(stylePath)
		.pipe(concat('lhc.dropdown.plugin.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js'));
});

var sourcemaps = require('gulp-sourcemaps');

gulp.task('js-static', function () {
	var stylePath = ['design/defaulttheme/js/js_static/*.js'];
	return gulp.src(stylePath)
		.pipe(sourcemaps.init({ loadMaps: true }))
		.pipe(terser())
		.pipe(sourcemaps.write('./'))
		.pipe(gulp.dest('design/defaulttheme/js/js_static'));
});

gulp.task('js-lh-canned', function () {
	var stylePath = ['design/defaulttheme/js/lh.cannedmsg.js'];

	return gulp.src(stylePath)
		.pipe(concat('lh.cannedmsg.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lh-notifications', function () {
	var stylePath = ['design/defaulttheme/js/lhc.notifications.js'];
	return gulp.src(stylePath)
		.pipe(concat('lhc.notifications.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lh-dashboard', function () {
	var stylePath = ['design/defaulttheme/js/lhc.dashboard.js'];
	return gulp.src(stylePath)
		.pipe(concat('lhc.dashboard.min.js'))
		.pipe(terser())
		.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-colorpicker', function () {
	var stylePath = ['design/defaulttheme/js/color-picker.js'];
	return gulp.src(stylePath).pipe(concat('color-picker.min.js'))
		.pipe(babel({
			presets: ['@babel/preset-env']
		}))
		.pipe(terser({ mangle: false, ecma: 5 }))
		.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lh-npm', function (done) {
	webpack(webpackConfig, function (err, stats) {
		if (err) throw new PluginError("webpack", err);
		console.log("[webpack]", stats.toString({
			// output options
		}));
	}); done();
});

gulp.task('js-cobrowse', gulp.series('js-cobrowse-operator', 'js-cobrowse-visitor', function () {

}));

gulp.task('default', gulp.series('js-lh-notifications', 'js-lh-dashboard', 'js-cobrowse-operator', 'js-cobrowse-visitor', 'js-modal-ext', 'js-angular-main', 'js-main-fileupload', 'js-datepicker', 'js-colorpicker', 'js-lhc-speak-js', 'js-lh', 'js-lh-legacy', 'js-lh-plugin', 'js-lh-canned', 'js-angular-checkmodel', 'js-angular-online', 'js-lh-npm'));

gulp.task('webpack', gulp.series('js-lh-npm'));