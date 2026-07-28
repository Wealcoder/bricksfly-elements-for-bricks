/**
 * Gulp Config — Bricks Animation Addons
 * Generates a distributable plugin zip.
 *
 * Includes: all PHP, TXT, and every folder in the plugin root.
 * Excludes: node_modules, src, dist (build output), and the gulpfile itself.
 *
 * Usage:
 *   npx gulp zip          # build dist/the-bricksfly.zip
 *   npx gulp watch:zip    # rebuild on change
 */

const path = require('path');
const gulp = require('gulp');
const zip = require('gulp-zip');
const del = require('del');
const through = require('through2');

const PLUGIN_SLUG = 'bricksfly-elements-for-bricks';
const DIST_DIR = 'dist';

const sources = [
    '**/*',
    '!node_modules/**', '!node_modules',
    // Source font files are excluded — webpack emits hashed copies into
    // public/build/fonts/ — but the OFL license text must ship with them.
    '!public/fonts/**', 'public/fonts/OFL.txt', '!src',
    '!src/**', '!src',
    '!dist/**', '!dist',
    '!gulpfile.js',
    '!babel.config.js',
    '!webpack.config.js',
    '!postcss.config.js',
    '!package.json',
    '!package-lock.json',
    '!config.json',
    '!jsconfig.json',
    '!postcss.config.js',
    '!tailwind.*.js',
    '!swap-config.js',
    '!webpack.config.dev',
    '!webpack.config.production',
    '!**/*.map',
    '!**/.DS_Store',
];

// Prefix every file path with the plugin slug so the zip extracts
function prefixFolder(folder) {
    return through.obj(function (file, _, cb) {
        if (file.relative) {
            file.path = path.join(file.base, folder, file.relative);
        }
        cb(null, file);
    });
}

gulp.task('clean:zip', () => {
    return del([`${DIST_DIR}/${PLUGIN_SLUG}.zip`]);
});

gulp.task('zip', gulp.series('clean:zip', () => {
    return gulp.src(sources, { base: '.', dot: false })
        .pipe(prefixFolder(PLUGIN_SLUG))
        .pipe(zip(`${PLUGIN_SLUG}.zip`))
        .pipe(gulp.dest(DIST_DIR));
}));

gulp.task('watch:zip', () => {
    return gulp.watch(
        sources,
        { ignoreInitial: false },
        gulp.series('zip')
    );
});

gulp.task('default', gulp.series('zip'));
