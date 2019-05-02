'use strict';

var angular = require('angular');

require('../core');

angular.module('app.products', [
  'app.core',
  'app.types',
  'app.categories',
  'app.frameconfigurations'
]);


require('./routes');
require('./products.controller');
require('./productsview.controller');
require('./products.service');
require('./inputtypes.service');
require('../categories/categories.service');
require('../types/types.service');
require('../frameconfigurations/frameconfigurations.service');
