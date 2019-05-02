
    'use strict';
var angular = require('angular');



    angular
        .module('app.dashboard')
        .service('OrderService', OrderService);

    OrderService.$inject = ['$http'];

    /* @ngInject */
    function OrderService($http) {

        var api = {
          loadByOrderUID: loadByOrderUID
        };

        return api;

        /////////////////////////////////////////


        function loadByOrderUID(orderuid) {
          return $http({
              url : '/orders/' + orderuid,
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }




    }
