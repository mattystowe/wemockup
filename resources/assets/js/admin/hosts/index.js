'use strict';

var angular = require('angular');

require('../core');

angular.module('app.hosts', [
  'app.core'
]);


require('./routes');
require('./hosts.controller');
require('./hosts.service');
