var gulp        = require('gulp');
var sass        = require('gulp-sass');
var minifyCss   = require('gulp-minify-css');
var include     = require('gulp-include');
var watch       = require('gulp-watch');


gulp.task('sass', function () {
    return gulp.src('./css/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(minifyCss({
            keepSpecialComments: 1
        }))
        .pipe(gulp.dest('./dist/'));
});

gulp.task('scripts', function() {
    return gulp.src(['./scripts/main.js'])
        .pipe( include() )
        .pipe( gulp.dest("./dist/") )
});

gulp.task('watch', function () {
    gulp.watch('./css/*.scss', ['sass'] );
    gulp.watch('./scripts/*.js', ['scripts'] );
});

gulp.task('build', ['scripts', 'sass']);