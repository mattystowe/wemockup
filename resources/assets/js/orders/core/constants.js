"use strict";

var angular = require('angular');
var toastr = require('toastr');
var config = require('./common').config();

angular.module('app.core')
        .constant('toastr', toastr);
