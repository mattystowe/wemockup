
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
          submitForProcessingAgain: submitForProcessingAgain,
          loadItemJobs: loadItemJobs,
          loadItemPostProcs: loadItemPostProcs,
          loadItemJobLog: loadItemJobLog,
          loadItemPostProcLog: loadItemPostProcLog,
          cancel: cancel,
          getInProgress: getInProgress
        };

        return api;

        /////////////////////////////////////////


        function getInProgress() {
          return $http({
              url : '/items/processing/',
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }


        function cancel(itemuid, reason) {
          return $http({
              url : '/orders/items/' + itemuid + '/cancel',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                reason: reason
              }
          });
        }



        function loadItemJobLog(itemjobid) {
          return $http({
              url : '/orders/itemjoblog/' + itemjobid,
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }

        function loadItemPostProcLog(itempostprocid) {
          return $http({
              url : '/orders/itempostproclog/' + itempostprocid,
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }



        function loadItemPostProcs(itemuid) {
          return $http({
              url : '/orders/itempostprocs/' + itemuid,
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }



        function loadItemJobs(itemuid, page) {
          return $http({
              url : '/orders/itemjobs/' + itemuid + '?page=' + page,
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }




        function loadByItemUID(itemuid) {
          return $http({
              url : '/orders/itemswithstats/' + itemuid,
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
