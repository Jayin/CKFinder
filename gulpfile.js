const gulp = require('gulp')
const path = require('path')
const uglify = require('gulp-uglify')
const imagemin = require('gulp-imagemin')
const nano = require('gulp-cssnano')
const del = require('del')

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
   return gulp.src(['./index.html', 'zkuploader.js', 'config.js', 'config.php'])
    .pipe(gulp.dest(DEST))
})

gulp.task('copy:README',()=>{
   return gulp.src(['./README.md'])
    .pipe(gulp.dest(DEST))
})

gulp.task('copy', ['copy:core', 'copy:libs', 'copy:lang', 'copy:indexs', 'copy:README'])

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

gulp.task('css:skins-core', ()=>{
  return gulp.src(['./skins/core/**/*.css'])
    .pipe(nano({
       zindex: false
    }))
    .pipe(gulp.dest(path.join(DEST, 'skins', 'core')))
})

gulp.task('css:skins-ztb', ()=>{
  return gulp.src(['./skins/ztb/**/*.css'])
    .pipe(nano({
       zindex: false
    }))
    .pipe(gulp.dest(path.join(DEST, 'skins', 'ztb')))
})


gulp.task('css', ['css:skins-core', 'css:skins-ztb'])


gulp.task('imagemin:skins', ()=>{
    return gulp.src(['./skins/**/*.png', './skins/**/*.gif', './skins/**/*.jpg'])
      .pipe(imagemin({optimizationLevel: 5}))
      .pipe(gulp.dest(path.join(DEST, 'skins')));
})

gulp.task('imagemin', ['imagemin:skins'])

gulp.task('clean-up', ['copy', 'uglify', 'css', 'imagemin'], ()=>{
  del([path.join(DEST, 'skins', 'moono')])
})

gulp.task('default', ['clean-up'])
