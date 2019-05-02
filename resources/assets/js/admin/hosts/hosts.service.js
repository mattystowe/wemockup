
    'use strict';
var angular = require('angular');



    angular
        .module('app.hosts')
        .service('HostsService', HostsService);

    HostsService.$inject = ['$http'];

    /* @ngInject */
    function HostsService($http) {

        var api = {
          gethealthyhosts: gethealthyhosts
        };

        return api;

        /////////////////////////////////////////


        function gethealthyhosts() {
          return $http({
              url : '/hosts/healthy',
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }




    }
