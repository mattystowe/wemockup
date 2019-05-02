'use strict';

var angular = require('angular');

require('../core');

angular.module('app.order', [
  'app.core',
  'app.orders'
]);


require('./routes');
require('./order.controller');
