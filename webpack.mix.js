const mix = require('laravel-mix');

mix.options({
    terser: {
        extractComments: false,
    }
});

mix.setPublicPath('public').js('resources/js/app.js', 'public/js').vue().version();
