'use strict';

var angular = require('angular');

require('../core');

angular.module('app.frameconfigurations', [
  'app.core'
]);

require('./routes');
require('./frameconfigurations.controller');
require('./frameconfigurations.service');
