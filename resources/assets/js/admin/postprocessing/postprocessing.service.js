
    'use strict';
var angular = require('angular');



    angular
        .module('app.postprocessing')
        .service('PostprocessingService', PostprocessingService);

    PostprocessingService.$inject = ['$http'];

    /* @ngInject */
    function PostprocessingService($http) {

        var api = {
          getAll: getAll,
          add: add,
          edit: edit,
          destroy: destroy,
          addToSku: addToSku,
          removeFromSku: removeFromSku,
          saveOrdering: saveOrdering
        }

        return api;

        /////////////////////////////////////////



        /**
         * Save the ordering of the post procs for a sku
         *
         *
         *
         * @param  {[type]} orderValues [description]
         * @param  {[type]} sku         [description]
         * @return {[type]}             [description]
         */
        function saveOrdering(orderValues, sku) {
          return $http({
              url : '/skus/' + sku.id + '/postprocs/order',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                orderValues: orderValues
              }
          });
        }

        /**
         * Associate a post processing stage with a sku with a priority level
         *
         *
         *
         * @param {[type]} postprocid [description]
         * @param {[type]} sku        [description]
         * @param {[type]} priority   [description]
         */
        function addToSku(postprocid, sku, priority) {
          return $http({
              url : '/skus/' + sku.id + '/postprocs',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                postprocid: postprocid,
                priority: priority
              }
          });
        }



        function removeFromSku(postprocid, sku) {
          return $http({
              url : '/skus/' + sku.id + '/postprocs/remove',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                postprocid: postprocid
              }
          });
        }




        function getAll() {
          return $http({
              url : '/postprocessing/',
              method : 'GET',
              headers : {
            'Content-Type' : 'application/json'
              }
          });
        }


        function add(step) {
          return $http({
              url : '/postprocessing/',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                name: step.name,
                jobname: step.jobname,
                data: step.data
              }
          });
        }

        function edit(step) {
          return $http({
              url : '/postprocessing/edit/',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                id: step.id,
                name: step.name,
                jobname: step.jobname,
                data: step.data
              }
          });
        }



        function destroy(step) {
          return $http({
              url : '/postprocessing/destroy/' + step.id,
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }



    }
