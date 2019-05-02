'use strict';

var angular = require('angular');

require('../core');

angular.module('app.categories', [
  'app.core'
]);

require('./routes');
require('./categories.controller');
require('./categories.service');
