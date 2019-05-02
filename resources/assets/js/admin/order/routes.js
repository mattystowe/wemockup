
'use strict';
var angular = require('angular');

angular
    .module('app.order')
    .run(appRun);

appRun.$inject = ['routerHelper'];


function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
}

function getStates() {
    return [
      {
          state: 'order',
          config: {
              url: '/order/{orderuid}',
              templateUrl: 'admin/order/index.html'
          }
      }
    ];
}
