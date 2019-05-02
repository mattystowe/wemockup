'use strict';

var angular = require('angular');

require('../core');

angular.module('app.skus', [
  'app.core',
  'app.order',
  'app.frameconfigurations',
  'app.postprocessing'
]);


require('./routes');
require('./skusview.controller');
require('./skus.service');
