'use strict';

var angular = require('angular');

require('../core');

angular.module('app.dashboard', [
  'app.core'
]);

require('./dashboard.controller');
require('./order.service');
require('./routes');
