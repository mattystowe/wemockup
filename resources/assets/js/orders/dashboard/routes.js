
'use strict';
var angular = require('angular');

angular
    .module('app.dashboard')
    .run(appRun);

appRun.$inject = ['routerHelper'];


function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
}

function getStates() {
    return [
      {
          state: 'dashboard',
          config: {
              url: '/dashboard/{orderuid}',
              templateUrl: '/orders/dashboard/index.html'
          }
      }
    ];
}
