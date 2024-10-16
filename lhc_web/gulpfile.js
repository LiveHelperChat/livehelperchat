var gulp = require('gulp'),
gutil    = require('gulp-util'),
uglify   = require('gulp-uglify-es').default,
concat   = require('gulp-concat'),
watch 	 = require('gulp-watch'),
webpack = require('webpack'),
webpackConfig = require('./webpack.config.js'),
bower = require('gulp-bower');
var babel = require('gulp-babel');
var newer        = require('gulp-newer');
var eslint       = require('gulp-eslint');
var sourcemaps = require('gulp-sourcemaps');
var browserify = require('browserify');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');

gulp.task('js-hotkeys', function() {
	var stylePath = ['design/defaulttheme/js/jquery.hotkeys.js'];

	return gulp.src(stylePath)
		.pipe(concat('jquery.hotkeys.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-cobrowse-operator', function() {
	var stylePath = ['design/defaulttheme/js/cobrowse/mutation-summary.js',
	                 'design/defaulttheme/js/cobrowse/tree-mirror.js',
	                 'design/defaulttheme/js/cobrowse/jquery.selector.js',
	                 'design/defaulttheme/js/cobrowse/lhc_operator.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('cobrowse.operator.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('design/defaulttheme/js/cobrowse/compiled'));
});

gulp.task('js-cobrowse-visitor', function() {
	var stylePath = ['design/defaulttheme/js/cobrowse/mutation-summary.js',
	                 'design/defaulttheme/js/cobrowse/tree-mirror.js',
	                 'design/defaulttheme/js/cobrowse/lhc.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('cobrowse.visitor.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('design/defaulttheme/js/cobrowse/compiled'));
});

gulp.task('js-angular-main', function() {
	var stylePath = ['design/defaulttheme/js/angular.lhc.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('angular.lhc.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-modal-ext', function() {
	var stylePath = ['design/defaulttheme/js/modal.ext.js'];
	return gulp.src(stylePath)
	.pipe(concat('modal.ext.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('design/defaulttheme/js'));
});


gulp.task('js-angular-online', function() {
	var stylePath = ['design/defaulttheme/js/angular.lhc.online.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('angular.lhc.online.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-angular-checkmodel', function() {
	var stylePath = ['design/defaulttheme/js/checklist-model.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('checklist-model.min.js'))
	.pipe(uglify())
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
	.pipe(uglify())
	.pipe(gulp.dest('design/defaulttheme/js/fileupload'));
});

gulp.task('js-datepicker', function() {
	var stylePath = ['design/defaulttheme/js/datepicker.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('datepicker.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lhc-speak-js', function() {
	var stylePath = ['design/defaulttheme/js/lhc.speak.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('lhc.speak.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lh', function() {
	var stylePath = ['design/defaulttheme/js/lh.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('lh.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lh-legacy', function() {
	var stylePath = ['design/defaulttheme/js/lh.legacy.js'];
	return gulp.src(stylePath)
	.pipe(concat('lh.legacy.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lh-plugin', function() {
	var stylePath = ['design/defaulttheme/js/lhc.dropdown.plugin.js'];
	return gulp.src(stylePath)
	.pipe(concat('lhc.dropdown.plugin.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('design/defaulttheme/js'));
});

var sourcemaps = require('gulp-sourcemaps');

gulp.task('js-static', function() {
    var stylePath = ['design/defaulttheme/js/js_static/*'];
    return gulp.src(stylePath)
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(uglify())
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('design/defaulttheme/js/js_static'));
});

gulp.task('js-lh-canned', function() {
	var stylePath = ['design/defaulttheme/js/lh.cannedmsg.js'];
	
	return gulp.src(stylePath)
	.pipe(concat('lh.cannedmsg.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lh-dashboard', function() {
	var stylePath = ['design/defaulttheme/js/lhc.dashboard.js'];	
	return gulp.src(stylePath)
	.pipe(concat('lhc.dashboard.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-colorpicker', function() {
	var stylePath = ['design/defaulttheme/js/color-picker.js'];
	return gulp.src(stylePath).pipe(concat('color-picker.min.js'))
    .pipe(babel({
            presets: ['@babel/preset-env']
        }))
	.pipe(uglify({mangle: false, ecma: 5}))
	.pipe(gulp.dest('design/defaulttheme/js'));
});

gulp.task('js-lh-npm', function(done) {
	 webpack(webpackConfig, function(err, stats) {
	        if(err) throw new gutil.PluginError("webpack", err);
	        gutil.log("[webpack]", stats.toString({
	            // output options
	        }));	            	
	 });
	 done();
});

gulp.task('bower', function() {
	return bower()
	.pipe(gulp.dest("./bower_components"));
});

gulp.task('bower-move-bootstrap',gulp.series('bower', function() {
	gulp.src('./bower_components/bootstrap/dist/js/!**.*').pipe(gulp.dest('./design/defaulttheme/vendor/bootstrap/js'));
	gulp.src('./bower_components/bootstrap/dist/fonts/!**.*').pipe(gulp.dest('./design/defaulttheme/vendor/bootstrap/fonts'));
	gulp.src('./bower_components/bootstrap/dist/css/bootstrap.min.css').pipe(gulp.dest('./design/defaulttheme/vendor/bootstrap/css'));
}));

gulp.task('bower-move-bootstrap-font',gulp.series('bower', function() {
	gulp.src('./bower_components/bootstrap/dist/fonts/!**.*').pipe(gulp.dest('./design/defaulttheme/fonts'));
}));

gulp.task('bower-move-material-font',gulp.series('bower', function() {
	gulp.src('./bower_components/material-design-icons/iconfont/MaterialIcons-Regular.eot').pipe(gulp.dest('./design/defaulttheme/fonts'));
	gulp.src('./bower_components/material-design-icons/iconfont/MaterialIcons-Regular.ttf').pipe(gulp.dest('./design/defaulttheme/fonts'));
	gulp.src('./bower_components/material-design-icons/iconfont/MaterialIcons-Regular.woff').pipe(gulp.dest('./design/defaulttheme/fonts'));
	gulp.src('./bower_components/material-design-icons/iconfont/MaterialIcons-Regular.woff2').pipe(gulp.dest('./design/defaulttheme/fonts'));
}));

gulp.task('bower-move-jquery',gulp.series('bower', function() {
	gulp.src('./bower_components/jquery/dist/!**.*').pipe(gulp.dest('./design/defaulttheme/vendor/jquery'));
}));

gulp.task('bower-move-metismenu',gulp.series('bower', function() {
	gulp.src('./bower_components/metisMenu/dist/!**.*').pipe(gulp.dest('./design/defaulttheme/vendor/metisMenu'));
}));

gulp.task('bower-setup',gulp.series('bower-move-bootstrap','bower-move-jquery','bower-move-bootstrap-font','bower-move-metismenu','bower-move-material-font', function() {
	
}));

gulp.task('js-cobrowse',gulp.series('js-cobrowse-operator','js-cobrowse-visitor', function() {

}));

//bower setup
gulp.task('bower-setup');

gulp.task('default', gulp.series('js-lh-dashboard','js-cobrowse-operator','js-cobrowse-visitor','js-modal-ext','js-angular-main','js-main-fileupload','js-datepicker','js-colorpicker','js-lhc-speak-js','js-lh','js-lh-legacy','js-lh-plugin','js-lh-canned','js-angular-checkmodel','js-angular-online','js-lh-npm'));

gulp.task('webpack', gulp.series('js-lh-npm'));

gulp.task('watch', function () {
	gulp.watch('design/defaulttheme/js/cobrowse/*.js', ['js-cobrowse-visitor','js-cobrowse-operator']);	
	gulp.watch('design/defaulttheme/js/lh.js', ['js-lh']);
	gulp.watch(['design/defaulttheme/js/lh/lh.js','design/defaulttheme/js/lh/lh-modules/*.js'], ['js-lh-npm']);
});