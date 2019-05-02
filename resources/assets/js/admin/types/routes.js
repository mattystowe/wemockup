
'use strict';
var angular = require('angular');

angular
    .module('app.types')
    .run(appRun);

appRun.$inject = ['routerHelper'];


function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
}

function getStates() {
    return [
      {
          state: 'types',
          config: {
              url: '/types',
              templateUrl: 'admin/types/index.html'
          }
      }
    ];
}
