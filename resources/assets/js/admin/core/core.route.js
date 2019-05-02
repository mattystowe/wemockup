
'use strict';
var angular = require('angular');

angular
    .module('app.core')
    .run(appRun);


appRun.$inject = ['routerHelper'];


    function appRun(routerHelper) {
        var otherwise = 'dashboard';
        routerHelper.configureStates(getStates(), otherwise);
    }

    function getStates() {
        return [
            {
                state: '404',
                config: {
                    url: '/404',
                    templateUrl: 'admin/core/404.html',
                    title: '404'
                }
            }
        ];
    }
