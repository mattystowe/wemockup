'use strict';

//jquery expects global window access
window.$ = window.jQuery = require('jquery')
require('bootstrap-sass');
//Angular app
var angular = require('angular');

require('./core');
require('./layout');
require('./dashboard');
require('./item');


angular.module('app', [
  'app.core',
  'app.layout',
  'app.dashboard',
  'app.item'
]);
