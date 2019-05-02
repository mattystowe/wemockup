'use strict';

//jquery expects global window access
window.$ = window.jQuery = require('jquery')
require('bootstrap-sass');
//Angular app
var angular = require('angular');

require('./core');
require('./layout');
require('./dashboard');
require('./categories');
require('./frameconfigurations');
require('./postprocessing');
require('./types');
require('./products');
require('./skus');
require('./orders');
require('./order');
require('./item');
require('./hosts');

angular.module('app', [
  'app.core',
  'app.dashboard',
  'app.categories',
  'app.types',
  'app.frameconfigurations',
  'app.postprocessing',
  'app.products',
  'app.skus',
  'app.orders',
  'app.order',
  'app.item',
  'app.hosts',
  'app.layout'
]);
