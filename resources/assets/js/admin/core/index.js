"use strict";

var angular = require('angular');



require('../blocks/logger');
require('../blocks/exception');
require('../blocks/router');
require('../blocks/filepicker');
require('angular-animate');
require('angular-loading-bar');
require('sweetalert');
require('ng-sortable');
require('chart.js');
require('angular-chart.js');


require('../../../../../bower_components/ngSweetAlert/SweetAlert');
require('textangular/dist/textAngular-sanitize.min');

angular.module('app.core', [
    'ngAnimate',
    'blocks.exception',
    'blocks.logger',
    'blocks.router',
    'blocks.filepicker',
    'ui.router',
    'angular-loading-bar',
    'oitozero.ngSweetAlert',
    'as.sortable',
    'chart.js',
    require('textAngular')
]);


require('./config');
require('./constants');
require('./core.route');
