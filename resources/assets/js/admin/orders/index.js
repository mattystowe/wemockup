'use strict';

var angular = require('angular');

require('../core');

angular.module('app.orders', [
  'app.core'
]);


require('./routes');
require('./orders.controller');
require('./orders.service');
