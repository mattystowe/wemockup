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


require('../../../../../bower_components/ngSweetAlert/SweetAlert');
require('textangular/dist/textAngular-sanitize.min');
require('../../../../../bower_components/angulartics/dist/angulartics.min.js');
require('../../../../../bower_components/angulartics-google-analytics/dist/angulartics-ga.min.js');


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
    require('textAngular'),
    'angulartics',
    'angulartics.google.analytics'
]);


require('./config');
require('./constants');
require('./core.route');
