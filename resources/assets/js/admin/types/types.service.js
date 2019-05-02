
    'use strict';
var angular = require('angular');



    angular
        .module('app.types')
        .service('TypesService', TypesService);

    TypesService.$inject = ['$http'];

    /* @ngInject */
    function TypesService($http) {

        var api = {
          getAll: getAll,
          destroy: destroy,
          add: add,
          edit: edit
        }

        return api;

        /////////////////////////////////////////

        function getAll() {
          return $http({
              url : '/types/',
              method : 'GET',
              headers : {
            'Content-Type' : 'application/json'
              }
          });
        }



        function destroy(type) {
          return $http({
              url : '/types/destroy/' + type.id,
              method : 'GET',
              headers : {
            'Content-Type' : 'application/json'
              }
          });
        }


        function add(type) {
          return $http({
              url : '/types/',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                name: type.name,
                jobname: type.jobname
              }
          });
        }

        function edit(type) {
          return $http({
              url : '/types/edit',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                id: type.id,
                name: type.name,
                jobname: type.jobname
              }
          });
        }

    }
