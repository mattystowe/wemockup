
    'use strict';
var angular = require('angular');



    angular
        .module('app.skus')
        .service('SkuService', SkuService);

    SkuService.$inject = ['$http'];

    /* @ngInject */
    function SkuService($http) {

        var api = {
          load: load
        }

        return api;

        /////////////////////////////////////////



        function load(id) {
          return $http({
              url : '/products/sku/' + id,
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }





    }
