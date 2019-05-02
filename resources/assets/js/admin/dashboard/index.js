'use strict';

var angular = require('angular');

require('../item');
require('../core');

angular.module('app.dashboard', [
  'app.item',
  'app.core'
]);

require('./routes');
require('./dashboard.controller');
