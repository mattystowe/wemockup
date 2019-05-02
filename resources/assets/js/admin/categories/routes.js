
'use strict';
var angular = require('angular');

angular
    .module('app.categories')
    .run(appRun);

appRun.$inject = ['routerHelper'];


function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
}

function getStates() {
    return [
      {
          state: 'categories',
          config: {
              url: '/categories',
              templateUrl: 'admin/categories/index.html'
          }
      }
    ];
}
