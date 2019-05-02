'use strict';

var angular = require('angular');

require('../core');

angular.module('app.postprocessing', [
  'app.core'
]);

require('./routes');
require('./postprocessing.controller');
require('./postprocessing.service');
