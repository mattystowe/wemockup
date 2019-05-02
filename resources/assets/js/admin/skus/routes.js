
'use strict';
var angular = require('angular');

angular
    .module('app.skus')
    .run(appRun);

appRun.$inject = ['routerHelper'];


function appRun(routerHelper) {
    routerHelper.configureDefaults('/skus','/skus/list');
    routerHelper.configureStates(getStates());
}

function getStates() {
    return [
      {
          state: 'skus',
          config: {
              url: '/skus',
              templateUrl: 'admin/skus/index.html'
          }
      },
      {
          state: 'skus.list',
          config: {
              url: '/list',
              templateUrl: 'admin/skus/list/index.html'
          }
      },
      {
          state: 'skus.view',
          config: {
              url: '/view/{skuid}',
              templateUrl: 'admin/skus/view/index.html',
          }
      }
    ];
}
