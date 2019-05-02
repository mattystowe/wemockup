
    'use strict';
var angular = require('angular');



    angular
        .module('app.categories')
        .service('CategoriesService', CategoriesService);

    CategoriesService.$inject = ['$http'];

    /* @ngInject */
    function CategoriesService($http) {

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
              url : '/categories/',
              method : 'GET',
              headers : {
            'Content-Type' : 'application/json'
              }
          });
        }



        function destroy(category) {
          return $http({
              url : '/categories/destroy/' + category.id,
              method : 'GET',
              headers : {
            'Content-Type' : 'application/json'
              }
          });
        }


        function add(category) {
          return $http({
              url : '/categories',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                name: category.name
              }
          });
        }

        function edit(category) {
          return $http({
              url : '/categories/edit',
              method : 'POST',
              headers : {
                'Content-Type' : 'application/json'
              },
              data : {
                id: category.id,
                name: category.name
              }
          });
        }

    }
