"use strict";

var angular = require('angular');

angular.module('blocks.exception', ['blocks.logger']);

require('./exception');
require('./exception-handler.provider');