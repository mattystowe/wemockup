
'use strict';
var angular = require('angular');

angular
    .module('app.postprocessing')
    .run(appRun);

appRun.$inject = ['routerHelper'];


function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
}

function getStates() {
    return [
      {
          state: 'postprocessing',
          config: {
              url: '/postprocessing',
              templateUrl: 'admin/postprocessing/index.html'
          }
      }
    ];
}
