const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.sass("resources/scss/style.scss", "public/site/style.css").options({
    processCssUrls: false,
});

mix.postCss("resources/css/app.css", "public/site/css/").postCss(
    "resources/css/jquery-ui.css",
    "public/site/css/"
);

mix.js("node_modules/bootstrap-select/js/bootstrap-select", "public/site/js/");

mix.js("resources/js/app.js", "public/site/js/app.js").sourceMaps();

mix.copy("resources/css/fonts", "public/site/css/fonts");
mix.copy("resources/css/icons", "public/site/css/icons");
mix.copy("resources/dist", "public/site/dist");
mix.copy("resources/assets", "public/assets");
