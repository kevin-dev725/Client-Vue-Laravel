let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.setPublicPath(path.normalize('public/frontend'))
  .js('resources/assets/js/frontend/app.js', 'js')
  .autoload({
      jquery: ['$', 'jQuery'],
      'popper.js': ['Popper']
  })
  .extract(['vue', 'jquery', 'lodash', 'bootstrap', 'chart.js' ,'axios'])
  .sass('resources/assets/sass/frontend/app.scss', 'css');