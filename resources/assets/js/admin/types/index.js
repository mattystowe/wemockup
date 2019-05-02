'use strict';

var angular = require('angular');

require('../core');

angular.module('app.types', [
  'app.core'
]);

require('./routes');
require('./types.controller');
require('./types.service');
