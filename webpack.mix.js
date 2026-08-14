const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
       require('tailwindcss'),
       require('autoprefixer'),
   ])
   .copy('resources/views/sw.js', 'public/sw.js')
   .copy('resources/views/manifest.json', 'public/manifest.json')
   .version();
