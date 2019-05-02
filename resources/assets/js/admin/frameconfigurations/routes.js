
'use strict';
var angular = require('angular');

angular
    .module('app.frameconfigurations')
    .run(appRun);

appRun.$inject = ['routerHelper'];


function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
}

function getStates() {
    return [
      {
          state: 'frameconfigurations',
          config: {
              url: '/frameconfigurations',
              templateUrl: 'admin/frameconfigurations/index.html'
          }
      }
    ];
}
