
'use strict';
var angular = require('angular');

angular
    .module('app.products')
    .run(appRun);

appRun.$inject = ['routerHelper'];


function appRun(routerHelper) {
    routerHelper.configureDefaults('/products','/products/list');
    routerHelper.configureStates(getStates());
}

function getStates() {
    return [
      {
          state: 'products',
          config: {
              url: '/products',
              templateUrl: 'admin/products/index.html'
          }
      },
      {
          state: 'products.list',
          config: {
              url: '/list',
              templateUrl: 'admin/products/list/index.html'
          }
      },
      {
          state: 'products.view',
          config: {
              url: '/view/{productid}',
              templateUrl: 'admin/products/view/index.html',
          }
      }
    ];
}
