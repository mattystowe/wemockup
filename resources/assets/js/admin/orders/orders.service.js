
    'use strict';
var angular = require('angular');



    angular
        .module('app.orders')
        .service('OrderService', OrderService);

    OrderService.$inject = ['$http'];

    /* @ngInject */
    function OrderService($http) {

        var api = {
          searchOrders: searchOrders,
          loadByOrderUID: loadByOrderUID,
          createTestOrder: createTestOrder
        };

        return api;

        /////////////////////////////////////////


        function createTestOrder(sku, order) {
          return $http({
              url : '/orders/createtestorder/' + sku,
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                email: order.email,
                firstname: order.firstname,
                lastname: order.lastname
              }
          });
        }




        function loadByOrderUID(orderuid) {
          return $http({
              url : '/orders/' + orderuid,
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }





        function searchOrders(query,page) {
          return $http({
              url : '/ordersearch/' + query + '?page=' + page,
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }




    }
