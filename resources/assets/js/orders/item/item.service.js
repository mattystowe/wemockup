
    'use strict';
var angular = require('angular');



    angular
        .module('app.item')
        .service('ItemService', ItemService);

    ItemService.$inject = ['$http'];

    /* @ngInject */
    function ItemService($http) {

        var api = {
          loadByItemUID: loadByItemUID,
          submitForProcessing: submitForProcessing,
          submitForProcessingAgain: submitForProcessingAgain
        };

        return api;

        /////////////////////////////////////////


        function loadByItemUID(itemuid) {
          return $http({
              url : '/orders/items/' + itemuid,
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }


        function submitForProcessing(itemuid, inputoptions) {
          var inputoptionsjson = angular.toJson(inputoptions);
          return $http({
              url : '/orders/items/' + itemuid + '/submitforprocessing',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                inputoptions: inputoptionsjson
              }
          });
        }


        /**
         * Attempt reprocessing of failed item
         *
         *
         * @param  {[type]} itemuid      [description]
         * @param  {[type]} inputoptions [description]
         * @return {[type]}              [description]
         */
        function submitForProcessingAgain(itemuid) {
          return $http({
              url : '/orders/items/' + itemuid + '/resubmitforprocessing',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }


    }
