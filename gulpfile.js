const gulp = require('gulp')
const path = require('path')
const uglify = require('gulp-uglify')
const imagemin = require('gulp-imagemin')
const nano = require('gulp-cssnano')

const DEST = './dist'

gulp.task('copy:core',()=>{
   return gulp.src(['./core/**/*'])
    .pipe(gulp.dest(path.join(DEST, 'core')))
})

gulp.task('copy:libs',()=>{
   return gulp.src(['./libs/**/*'])
    .pipe(gulp.dest(path.join(DEST, 'libs')))
})

gulp.task('copy:lang',()=>{
   return gulp.src(['./lang/en.json', './lang/zh-cn.json'])
    .pipe(gulp.dest(path.join(DEST, 'lang')))
})

gulp.task('copy:indexs',()=>{
   return gulp.src(['./ckfinder.html', 'ckfinder.js', 'config.js', 'config.php'])
    .pipe(gulp.dest(DEST))
})

gulp.task('copy', ['copy:core', 'copy:libs', 'copy:lang', 'copy:indexs'])

gulp.task('uglify:plugins', ()=>{
  return gulp.src(['./plugins/**/*.js'])
    .pipe(uglify())
    .pipe(gulp.dest(path.join(DEST, 'plugins')))
})

gulp.task('uglify:skins', ()=>{
  return gulp.src(['./skins/**/*.js'])
    .pipe(uglify())
    .pipe(gulp.dest(path.join(DEST, 'skins')))
})

gulp.task('uglify', ['uglify:plugins', 'uglify:skins'])

gulp.task('css:skins', ()=>{
  return gulp.src(['./skins/**/*.css'])
    .pipe(nano({
       zindex: false
    }))
    .pipe(gulp.dest(path.join(DEST, 'skins')))
})


gulp.task('css', ['css:skins'])


gulp.task('imagemin:skins', ()=>{
    return gulp.src(['./skins/**/*.png', './skins/**/*.gif', './skins/**/*.jpg'])
      .pipe(imagemin({optimizationLevel: 5}))
      .pipe(gulp.dest(path.join(DEST, 'skins')));
})

gulp.task('imagemin', ['imagemin:skins'])

gulp.task('default', ['copy', 'uglify', 'css', 'imagemin'])