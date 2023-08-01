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

mix.sass('resources/assets/sass/backend/app.scss', 'public/css/backendLibrary.css')
    .styles([
        'resources/assets/css/backend/app.css',
    ], 'public/css/backend.css')
    .js([
        'resources/assets/js/app.js',
    ], 'public/js/backend.js')

    .js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css');
