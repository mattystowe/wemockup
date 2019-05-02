
'use strict';
var angular = require('angular');

angular
    .module('app.item')
    .run(appRun);

appRun.$inject = ['routerHelper'];


function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
}

function getStates() {
    return [
      {
          state: 'item',
          config: {
              url: '/item/{itemuid}',
              templateUrl: '/admin/item/index.html'
          }
      }
    ];
}
