var gulp = require('gulp');
var less = require('gulp-less');
var minifyCss = require('gulp-minify-css');
var concat = require('gulp-concat');
var browserSync = require('browser-sync').create();
var autoprefixer = require('gulp-autoprefixer');

gulp.task('less', function () {
  return gulp.src('./www/app/main.less')
    .pipe(less())
    .pipe(concat('style.css'))  
    .pipe(gulp.dest('./www/css'))
    .pipe(autoprefixer({
 			browsers: ['last 3 versions', 'IE 9'],
 			cascade: true
 		}))
    .pipe(browserSync.stream());
});

gulp.task('libs-js',function(){
    
});


 gulp.task('serve', ['less'], function () {
     browserSync.init({
         injectChanges: true,
         proxy: 'evzdelavani2.loc/'
     });

     gulp.watch('./www/app/components/*.less', ['less']);
 });

gulp.task('default', [
	'less'
]);