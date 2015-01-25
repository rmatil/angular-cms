'use strict';

var gulp = require('gulp');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');

var DEST = 'web/cms/js/build/';

gulp.task('default', function() {
    var files = ['app.js', 'directives.js', 'filters.js', 'genServices.js', 'services.js', 
                    'controller/articleCtrl.js', 'controller/controllers.js', 'controller/errorCtrl.js', 'controller/eventCtrl.js',
                    'controller/locationCtrl.js', 'controller/mediaCtrl.js', 'controller/overviewCtrl.js', 'controller/pageCtrl.js',
                    'controller/settingCtrl.js', 'controller/userCtrl.js']

    for (var i = 0; i < files.length; i++) {
        gulp.src('web/cms/js/' + files[i])
        // This will minify and rename to foo.min.js
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(gulp.dest(DEST));
    };
    return 
});