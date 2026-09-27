/**
 * Gulp: compile + minify on watch (no .map files)
 */

const gulp = require('gulp');
const plumber = require('gulp-plumber');
const babel = require('gulp-babel');
const rename = require('gulp-rename');
const terser = require('gulp-terser');
const cleanCSS = require('gulp-clean-css');
const autoprefixer = require('gulp-autoprefixer');

// Sass wrapper (uses your "sass" dep)
const dartSass = require('sass');                 // npm i -D gulp-sass if not installed
const gulpSass = require('gulp-sass')(dartSass);

function onError(err) { console.error(err.toString()); this.emit('end'); }

// ---------- JS ----------
gulp.task('compile:js', () =>
  gulp.src(['src/js/**/*.js', '!src/js/**/*.min.js'])
    .pipe(plumber({ errorHandler: onError }))
    .pipe(babel({
      presets: [['@babel/preset-env', { modules: false, targets: '> 0.5%, not dead' }]],
      sourceType: 'unambiguous',
    }))
    .pipe(gulp.dest('assets/js'))
);

gulp.task('minify:js', () =>
  gulp.src(['assets/js/**/*.js', '!assets/js/**/*.min.js'])
    .pipe(plumber({ errorHandler: onError }))
    .pipe(terser({ ecma: 2020, compress: { passes: 2 } }))
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('assets/js'))
);

// ---------- CSS ----------
gulp.task('compile:scss', () =>
  gulp.src(['src/scss/**/*.scss'])
    .pipe(plumber({ errorHandler: onError }))
    .pipe(gulpSass().on('error', gulpSass.logError))
    .pipe(autoprefixer())
    .pipe(gulp.dest('assets/css'))
);

gulp.task('minify:css', () =>
  gulp.src(['assets/css/**/*.css', '!assets/css/**/*.min.css'])
    .pipe(plumber({ errorHandler: onError }))
    .pipe(cleanCSS({ level: 2 }))
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('assets/css'))
);

// ---------- Combos ----------
gulp.task('buildJs', gulp.series('compile:js', 'minify:js'));
gulp.task('buildCss', gulp.series('compile:scss', 'minify:css'));
gulp.task('build', gulp.series('buildCss', 'buildJs'));

// ---------- Watch ----------
gulp.task('watch', () => {
  gulp.watch('src/js/**/*.js',   { ignoreInitial: true }, gulp.series('buildJs'));
  gulp.watch('src/scss/**/*.scss', { ignoreInitial: true }, gulp.series('buildCss'));
});
