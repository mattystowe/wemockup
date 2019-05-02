
    'use strict';
var angular = require('angular');



    angular
        .module('app.frameconfigurations')
        .service('FrameConfigurationsService', FrameConfigurationsService);

    FrameConfigurationsService.$inject = ['$http'];

    /* @ngInject */
    function FrameConfigurationsService($http) {

        var api = {
          getAll: getAll,
          add: add,
          edit: edit,
          destroy: destroy
        }

        return api;

        /////////////////////////////////////////

        function getAll() {
          return $http({
              url : '/frameconfigurations/',
              method : 'GET',
              headers : {
            'Content-Type' : 'application/json'
              }
          });
        }


        function add(frameconfig) {
          return $http({
              url : '/frameconfigurations',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                name: frameconfig.name,
                dimx: frameconfig.dimx,
                dimy: frameconfig.dimy,
                outputformat: frameconfig.outputformat,
                watermark: frameconfig.watermark
              }
          });
        }

        function edit(frameconfig) {
          return $http({
              url : '/frameconfigurations/edit/',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                id: frameconfig.id,
                name: frameconfig.name,
                dimx: frameconfig.dimx,
                dimy: frameconfig.dimy,
                outputformat: frameconfig.outputformat,
                watermark: frameconfig.watermark
              }
          });
        }



        function destroy(frameconfig) {
          return $http({
              url : '/frameconfigurations/destroy/' + frameconfig.id,
              method : 'GET',
              headers : {
                'Content-Type' : 'application/json'
              }
          });
        }



    }
