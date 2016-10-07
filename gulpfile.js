var gulp        = require('gulp');
var sass        = require('gulp-sass');
var minifyCss   = require('gulp-minify-css');
var include     = require('gulp-include');
var watch       = require('gulp-watch');
var del         = require('del');
var gulpCopy    = require('gulp-copy');
var merge       = require('merge-stream');


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

//Build Dist
gulp.task('clean', function () {
    return del([
        'WP-Places-I-Have-Been/**/*'
    ]);
});

gulp.task('copy', function () {
    var dist = gulp.src('./dist/*')
        .pipe(gulpCopy('./WP-Places-I-Have-Been/'));
    var flags = gulp.src('./flags/*')
        .pipe(gulpCopy('./WP-Places-I-Have-Been/'));
    var data = gulp.src('./data/*')
        .pipe(gulpCopy('./WP-Places-I-Have-Been/'));
    var assets = gulp.src('./assets/*')
        .pipe(gulpCopy('./WP-Places-I-Have-Been/'));
    var readme = gulp.src('./readme.txt')
        .pipe(gulpCopy('./WP-Places-I-Have-Been/'));
    var pihb = gulp.src('./WP-Places-I-Have-Been.php')
        .pipe(gulpCopy('./WP-Places-I-Have-Been/'));
    return merge(dist, flags, data, readme, pihb);
});

gulp.task('build', ['scripts', 'sass']);

gulp.task('dist', ['clean', 'scripts', 'sass', 'copy']);