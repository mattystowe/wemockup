
'use strict';
var angular = require('angular');

angular
    .module('app.hosts')
    .run(appRun);

appRun.$inject = ['routerHelper'];


function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
}

function getStates() {
    return [
      {
          state: 'hosts',
          config: {
              url: '/hosts',
              templateUrl: 'admin/hosts/index.html'
          }
      }
    ];
}
