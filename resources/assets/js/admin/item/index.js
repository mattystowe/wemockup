'use strict';

var angular = require('angular');

require('../core');

angular.module('app.item', [
  'app.core'
]);

require('./item.service');
require('./item.controller');
require('./routes');
