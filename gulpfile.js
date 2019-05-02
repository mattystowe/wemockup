var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {

    //Compile sass
    mix.sass('app.scss')
       .version('public/css/app.css');
    //mix in the fonts
    mix.copy('./node_modules/bootstrap-sass/assets/fonts', 'public/build/fonts');
    mix.copy('./node_modules/font-awesome/fonts','public/build/fonts')

    //copy image resources
    mix.copy('resources/assets/img/**/*.*', 'public/img/');

    //Compile custom styles
    mix.styles([
           'custom.css',
           '../../../node_modules/angular-loading-bar/build/loading-bar.min.css',
           '../../../node_modules/sweetalert/dist/sweetalert.css',
           '../../../node_modules/ng-sortable/dist/ng-sortable.min.css',
           '../../../node_modules/textangular/dist/textAngular.css'
       ],'public/css/custom.css');


    //CLIENT ORDERS INTERFACE
    //Mix in client app and copy partials to public view
    mix.browserify('orders/index.js','public/js/orders.js');
    mix.copy('resources/assets/js/orders/**/*.html', 'public/orders/');


    //ADMIN INTERFACE
    //Mix in admin app and copy partials to public view
    mix.browserify('admin/index.js','public/js/admin.js');
    mix.copy('resources/assets/js/admin/**/*.html', 'public/admin/');



});
