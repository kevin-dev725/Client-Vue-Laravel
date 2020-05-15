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

// mix.js('resources/assets/js/app.js', 'public/js')
//   .js('resources/assets/js/frontend/app.js', 'public/js/frontend')
//   .autoload({
//       jquery: ['$', 'jQuery'],
//       'popper.js': ['Popper']
//   })
//   .extract(['vue', 'jquery', 'lodash', 'bootstrap', 'chart.js' ,'axios'])
//   .sass('resources/assets/sass/app.scss', 'public/css')
//   .sass('resources/assets/sass/frontend/app.scss', 'public/css/frontend');

if (process.env.NODE_ENV === 'test') {
    const nodeExternals = require('webpack-node-externals');
    mix.webpackConfig({
        externals: [nodeExternals()]
    });
}

if (process.env.section) {
    require(`${__dirname}/webpack.mix.${process.env.section}.js`);
}