
'use strict';
var angular = require('angular');

angular
    .module('app.orders')
    .run(appRun);

appRun.$inject = ['routerHelper'];


function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
}

function getStates() {
    return [
      {
          state: 'orders',
          config: {
              url: '/orders',
              templateUrl: 'admin/orders/index.html'
          }
      }
    ];
}
